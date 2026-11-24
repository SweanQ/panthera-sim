<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['customer', 'product', 'payments', 'cancelledBy'])->latest()->get();

        $stats = [
            'total_orders' => $orders->count(),
            'in_production' => $orders->whereIn('production_status', ['desain_disetujui', 'proses_sablon'])->count(),
            'completed' => $orders->whereIn('production_status', ['selesai', 'diambil'])->count(),
            'cancelled_orders' => $orders->where('production_status', 'dibatalkan')->count(),
            'total_revenue' => Payment::sum('amount'),
        ];

        return view('orders.index', compact('orders', 'stats'));
    }

    public function create()
    {
        $customers = Customer::latest()->get();
        $products = Product::where('is_active', true)->get();
        return view('orders.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_mode' => 'required|in:existing,new',
            'customer_id' => 'required_if:customer_mode,existing|nullable|exists:customers,id',
            'new_customer_name' => 'required_if:customer_mode,new|nullable|string|max:100',
            'new_whatsapp_number' => 'required_if:customer_mode,new|nullable|string|max:20',
            'new_address' => 'nullable|string|max:255',
            'product_id' => 'required|exists:products,id,is_active,1',
            'size' => 'required|string|max:20',
            'color' => 'required|string|max:50',
            'quantity' => 'required|integer|min:1',
            'dp_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:tunai,transfer',
            'design_file' => 'nullable|image|max:2048',
        ]);

        if ($validated['customer_mode'] === 'new') {
            $customer = Customer::create([
                'name' => $validated['new_customer_name'],
                'whatsapp_number' => $validated['new_whatsapp_number'],
                'address' => $validated['new_address'] ?? null,
            ]);
            $customerId = $customer->id;
        } else {
            $customerId = $validated['customer_id'];
        }

        $product = Product::where('is_active', true)->findOrFail($validated['product_id']);
        $totalPrice = $product->base_price * $validated['quantity'];
        $minDp = 0.5 * $totalPrice;

        if ($validated['dp_amount'] < $minDp) {
            return back()
                ->withInput()
                ->withErrors(['dp_amount' => 'Nominal DP ditolak: minimal 50% dari total harga yaitu Rp ' . number_format($minDp, 0, ',', '.') . '.']);
        }

        $designPath = null;
        if ($request->hasFile('design_file')) {
            $designPath = $request->file('design_file')->store('designs', 'public');
        }

        $paymentStatus = $validated['dp_amount'] >= $totalPrice ? 'lunas' : 'dp_50';

        $order = Order::create([
            'order_code' => 'PNT-' . strtoupper((string) Str::ulid()),
            'customer_id' => $customerId,
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'size' => $validated['size'],
            'color' => $validated['color'],
            'quantity' => $validated['quantity'],
            'total_price' => $totalPrice,
            'design_file' => $designPath,
            'revision_count' => 0,
            'payment_status' => $paymentStatus,
            'production_status' => 'desain_disetujui',
            'order_source' => 'manual_web',
        ]);

        Payment::create([
            'order_id' => $order->id,
            'payment_type' => $paymentStatus === 'lunas' ? 'pelunasan' : 'dp',
            'amount' => $validated['dp_amount'],
            'payment_method' => $validated['payment_method'],
            'payment_date' => now()->toDateString(),
        ]);

        return redirect()->route('orders.index')->with('success', 'Pesanan ' . $order->order_code . ' berhasil dicatat & Invoice siap dicetak.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'production_status' => 'required|in:konsultasi,desain_disetujui,proses_sablon,selesai,diambil',
        ]);

        DB::transaction(function () use ($order, $validated) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $this->ensureNotCancelled($lockedOrder);

            $updates = ['production_status' => $validated['production_status']];
            if (
                $lockedOrder->production_started_at === null
                && in_array($validated['production_status'], ['proses_sablon', 'selesai', 'diambil'], true)
            ) {
                $updates['production_started_at'] = now();
            }

            $lockedOrder->update($updates);
        });

        return back()->with('success', 'Status produksi ' . $order->order_code . ' diperbarui menjadi ' . strtoupper(str_replace('_', ' ', $validated['production_status'])) . '.');
    }

    public function cancel(Request $request, Order $order)
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($request, $order, $validated) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

            if (! $lockedOrder->canBeCancelled()) {
                throw ValidationException::withMessages([
                    'cancellation' => 'Pesanan hanya dapat dibatalkan sebelum produksi dimulai.',
                ]);
            }

            $lockedOrder->update([
                'production_status' => 'dibatalkan',
                'cancelled_at' => now(),
                'cancelled_by' => $request->user()->id,
                'cancellation_reason' => $validated['reason'],
            ]);
        });

        return back()->with('success', 'Pesanan ' . $order->order_code . ' dibatalkan. Pembayaran yang tercatat tidak dihapus atau dikembalikan otomatis.');
    }

    public function destroy(Request $request, Order $order)
    {
        $request->validate([
            'confirmation' => ['required', 'string', Rule::in([$order->order_code])],
        ]);

        $orderCode = $order->order_code;
        DB::transaction(function () use ($order) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $lockedOrder->delete();
        });

        return redirect()->route('orders.index')
            ->with('success', 'Pesanan ' . $orderCode . ' beserta riwayat pembayarannya dihapus permanen.');
    }

    public function incrementRevision(Order $order)
    {
        $revisionCount = DB::transaction(function () use ($order) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $this->ensureNotCancelled($lockedOrder);

            if ($lockedOrder->revision_count >= 3) {
                return null;
            }

            $lockedOrder->increment('revision_count');

            return $lockedOrder->revision_count;
        });

        if ($revisionCount === null) {
            return back()->withErrors(['revision' => 'Batas maksimal 3x revisi gratis sudah tercapai.']);
        }

        return back()->with('success', 'Revisi desain ke-' . $revisionCount . ' berhasil dicatat.');
    }

    public function settlePayment(Order $order)
    {
        DB::transaction(function () use ($order) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $this->ensureNotCancelled($lockedOrder);

            $paid = $lockedOrder->payments()->sum('amount');
            $remaining = max(0, $lockedOrder->total_price - $paid);

            if ($remaining > 0) {
                Payment::create([
                    'order_id' => $lockedOrder->id,
                    'payment_type' => 'pelunasan',
                    'amount' => $remaining,
                    'payment_method' => 'transfer',
                    'payment_date' => now()->toDateString(),
                ]);
            }

            $lockedOrder->update(['payment_status' => 'lunas']);
        });

        return back()->with('success', 'Pelunasan pesanan ' . $order->order_code . ' berhasil dicatat.');
    }

    public function downloadInvoice(Order $order)
    {
        $order->load(['customer', 'product', 'payments', 'user']);
        $pdf = Pdf::loadView('orders.invoice_pdf', compact('order'));
        return $pdf->stream('Invoice-' . $order->order_code . '.pdf');
    }

    public function reports()
    {
        $payments = Payment::with(['order.customer', 'order.product'])->latest()->get();
        $totalIncome = $payments->sum('amount');
        $activeOrderTotal = Order::where('production_status', '!=', 'dibatalkan')->sum('total_price');
        $activePayments = Payment::whereHas('order', fn ($query) => $query->where('production_status', '!=', 'dibatalkan'))->sum('amount');
        $totalReceivable = max(0, $activeOrderTotal - $activePayments);

        return view('orders.reports', compact('payments', 'totalIncome', 'totalReceivable'));
    }

    private function ensureNotCancelled(Order $order): void
    {
        if ($order->production_status === 'dibatalkan' || $order->cancelled_at !== null) {
            throw ValidationException::withMessages([
                'order' => 'Pesanan yang sudah dibatalkan tidak dapat diubah atau dilunasi.',
            ]);
        }
    }
}