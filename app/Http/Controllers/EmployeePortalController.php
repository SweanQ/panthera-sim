<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\RawMaterial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;

class EmployeePortalController extends Controller
{
    public function dashboard(): View
    {
        return view('employee.dashboard', [
            'stats' => [
                'active_orders' => Order::whereNotIn('production_status', ['selesai', 'diambil', 'dibatalkan'])->count(),
                'apparel_variants' => ProductVariant::count(),
                'low_apparel' => ProductVariant::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->count(),
                'low_materials' => RawMaterial::whereColumn('current_stock', '<=', 'minimum_stock')->count(),
            ],
            'lowApparel' => ProductVariant::with('product')
                ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                ->orderBy('stock_quantity')
                ->limit(5)
                ->get(),
            'lowMaterials' => RawMaterial::whereColumn('current_stock', '<=', 'minimum_stock')
                ->orderBy('current_stock')
                ->limit(5)
                ->get(),
            'recentOrders' => Order::with(['customer', 'product'])->latest()->limit(5)->get(),
        ]);
    }

    public function orders(): View
    {
        return view('employee.orders', [
            'orders' => Order::with(['customer', 'product', 'cancelledBy'])->latest()->paginate(15),
        ]);
    }

    public function catalog(): View
    {
        return view('employee.catalog', [
            'products' => Product::withCount(['variants', 'orders'])->orderBy('name')->paginate(15),
        ]);
    }

    public function storeProduct(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'material' => ['nullable', 'string', 'max:255'],
            'stock_source' => ['required', 'in:mandiri,jakarta'],
            'base_price' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if (isset($validated['image'])) {
            $validated['image_path'] = $this->storeProductImage($validated['image']);
            unset($validated['image']);
        }

        Product::create($validated);

        return redirect()->route('inventory.catalog')->with('success', 'Produk katalog berhasil ditambahkan.');
    }

    public function updateProduct(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'material' => ['nullable', 'string', 'max:255'],
            'stock_source' => ['required', 'in:mandiri,jakarta'],
            'base_price' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $newImagePath = isset($validated['image'])
            ? $this->storeProductImage($validated['image'])
            : null;
        unset($validated['image']);

        $previousImagePath = $product->image_path;
        if ($newImagePath !== null) {
            $validated['image_path'] = $newImagePath;
        }

        $product->update($validated);

        if ($newImagePath !== null) {
            $this->deleteProductImage($previousImagePath, $product->id);
        }

        return back()->with('success', 'Data produk katalog berhasil diperbarui.');
    }

    public function archiveProduct(Product $product): RedirectResponse
    {
        $product->update(['is_active' => false]);

        return back()->with('success', 'Produk diarsipkan dan tidak lagi ditampilkan di katalog publik.');
    }

    public function restoreProduct(Product $product): RedirectResponse
    {
        $product->update(['is_active' => true]);

        return back()->with('success', 'Produk dipulihkan ke katalog publik.');
    }

    public function destroyProduct(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'confirmation' => ['required', 'string', 'max:255'],
        ]);

        $imagePath = DB::transaction(function () use ($validated, $product) {
            $lockedProduct = Product::query()->lockForUpdate()->findOrFail($product->id);

            if (! hash_equals($lockedProduct->name, $validated['confirmation'])) {
                throw ValidationException::withMessages([
                    'confirmation' => 'Nama produk yang diketik tidak cocok.',
                ]);
            }

            if ($lockedProduct->orders()->exists() || $lockedProduct->variants()->exists()) {
                throw ValidationException::withMessages([
                    'product' => 'Produk memiliki pesanan atau data stok. Arsipkan produk agar riwayat tetap aman.',
                ]);
            }

            $imagePath = $lockedProduct->image_path;
            $lockedProduct->delete();

            return $imagePath;
        });

        $this->deleteProductImage($imagePath, $product->id);

        return redirect()->route('inventory.catalog')->with('success', 'Produk dihapus permanen.');
    }

    private function storeProductImage(UploadedFile $image): string
    {
        $path = $image->store('products', 'public');

        if (! is_string($path) || $path === '') {
            throw new RuntimeException('Foto produk gagal disimpan.');
        }

        return $path;
    }

    private function deleteProductImage(?string $imagePath, int $productId): void
    {
        if ($imagePath !== null && ! Storage::disk('public')->delete($imagePath)) {
            Log::warning('Gagal menghapus foto produk.', [
                'product_id' => $productId,
                'image_path' => $imagePath,
            ]);
        }
    }
}
