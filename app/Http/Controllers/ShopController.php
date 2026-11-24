<?php

namespace App\Http\Controllers;

use App\Models\CustomInquiry;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:80'],
            'stock_source' => ['nullable', 'in:mandiri,jakarta'],
            'sort' => ['nullable', 'in:featured,price_asc,price_desc'],
        ]);

        $query = Product::where('is_active', true);

        if (! empty($filters['q'])) {
            $search = trim($filters['q']);
            $query->where(function ($products) use ($search) {
                $products->where('name', 'like', '%' . $search . '%')
                    ->orWhere('material', 'like', '%' . $search . '%');
            });
        }

        if (! empty($filters['stock_source'])) {
            $query->where('stock_source', $filters['stock_source']);
        }

        match ($filters['sort'] ?? 'featured') {
            'price_asc' => $query->orderBy('base_price'),
            'price_desc' => $query->orderByDesc('base_price'),
            default => $query->orderBy('name'),
        };

        return view('welcome', [
            'products' => $query->paginate(8)->withQueryString(),
            'inquiryProducts' => Product::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'filters' => [
                'q' => $filters['q'] ?? '',
                'stock_source' => $filters['stock_source'] ?? '',
                'sort' => $filters['sort'] ?? 'featured',
            ],
        ]);
    }

    public function createOrder(Request $request): View
    {
        return view('shop.order-form', [
            'products' => Product::where('is_active', true)->orderBy('name')->get(),
            'selectedProductId' => $request->integer('product_id'),
        ]);
    }

    public function storeOrder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'whatsapp_number' => $this->whatsappRules(),
            'product_id' => ['required', 'integer', 'exists:products,id,is_active,1'],
            'size' => ['required', 'string', 'max:20'],
            'color' => ['required', 'string', 'max:50'],
            'quantity' => ['required', 'integer', 'min:1', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $phone = $this->normalizePhone($validated['whatsapp_number']);
        $product = Product::where('is_active', true)->findOrFail($validated['product_id']);
        $user = $request->user();
        $order = DB::transaction(function () use ($validated, $phone, $product, $user) {
            if ($user?->role === 'customer') {
                $customer = Customer::firstOrNew(['user_id' => $user->id]);
                $customer->name = $user->name;
                $customer->user_id = $user->id;
            } else {
                $customer = new Customer(['name' => $validated['name']]);
            }

            $customer->whatsapp_number = $phone;
            $customer->save();

            return Order::create([
                'order_code' => 'PNT-' . strtoupper((string) \Illuminate\Support\Str::ulid()),
                'customer_id' => $customer->id,
                'product_id' => $product->id,
                'user_id' => null,
                'size' => $validated['size'],
                'color' => $validated['color'],
                'quantity' => $validated['quantity'],
                'total_price' => (float) $product->base_price * $validated['quantity'],
                'notes' => $validated['notes'] ?? null,
                'revision_count' => 0,
                'payment_status' => 'belum_dp',
                'production_status' => 'konsultasi',
                'order_source' => 'manual_web',
            ]);
        });

        $request->session()->put('panthera_order_confirmation', $order->id);

        return redirect()->route('shop.orders.confirmation');
    }

    public function orderConfirmation(Request $request): View|RedirectResponse
    {
        $orderId = $request->session()->get('panthera_order_confirmation');
        if (! $orderId) {
            return redirect()->route('home');
        }

        $order = Order::query()
            ->with(['product', 'customer'])
            ->findOrFail($orderId);

        return view('shop.order-confirmation', compact('order'));
    }

    public function storeInquiry(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'whatsapp_number' => $this->whatsappRules(),
            'product_id' => ['nullable', 'integer', 'exists:products,id,is_active,1'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:5000'],
            'details' => ['required', 'string', 'min:15', 'max:2000'],
        ]);

        $validated['whatsapp_number'] = $this->normalizePhone($validated['whatsapp_number']);
        CustomInquiry::create($validated);

        return redirect()->to(route('home') . '#inquiry')
            ->with('success', 'Permintaan konsultasi tersimpan. Tim Panthera akan menghubungi Anda melalui WhatsApp.');
    }

    public function trackingForm(): View
    {
        return view('shop.tracking');
    }

    public function trackOrder(Request $request): View|RedirectResponse
    {
        $validated = $request->validate([
            'order_code' => ['required', 'string', 'max:40'],
            'whatsapp_number' => $this->whatsappRules(),
        ]);

        $phone = $this->normalizePhone($validated['whatsapp_number']);
        $order = Order::query()
            ->with('product')
            ->where('order_code', strtoupper(trim($validated['order_code'])))
            ->whereHas('customer', fn ($query) => $query->where('whatsapp_number', $phone))
            ->first();

        if (! $order) {
            throw ValidationException::withMessages([
                'order_code' => 'Pesanan tidak ditemukan. Periksa kembali kode pesanan dan nomor WhatsApp.',
            ]);
        }

        return view('shop.tracking-result', compact('order'));
    }

    private function whatsappRules(): array
    {
        return [
            'required',
            'string',
            'max:30',
            function (string $attribute, mixed $value, \Closure $fail): void {
                $digits = $this->normalizePhone((string) $value);
                if (strlen($digits) < 8 || strlen($digits) > 15) {
                    $fail('Nomor WhatsApp harus berisi 8 sampai 15 digit.');
                }
            },
        ];
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (str_starts_with($digits, '0')) {
            return '62' . substr($digits, 1);
        }

        return str_starts_with($digits, '8') ? '62' . $digits : $digits;
    }
}
