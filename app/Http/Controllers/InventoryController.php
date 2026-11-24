<?php

namespace App\Http\Controllers;

use App\Models\ApparelStockMovement;
use App\Models\MaterialStockMovement;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\RawMaterial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function apparel(): View
    {
        return view('employee.inventory.apparel', [
            'products' => Product::where('is_active', true)->orderBy('name')->get(),
            'variants' => ProductVariant::with('product')->orderBy('product_id')->orderBy('size')->orderBy('color')->paginate(15),
        ]);
    }

    public function storeVariant(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'size' => ['required', 'string', 'max:20'],
            'color' => ['required', 'string', 'max:50'],
            'sku' => ['nullable', 'string', 'max:100', 'unique:product_variants,sku'],
            'initial_stock' => ['required', 'integer', 'min:0', 'max:2000000000'],
            'low_stock_threshold' => ['required', 'integer', 'min:0', 'max:2000000000'],
        ]);

        $alreadyExists = ProductVariant::query()
            ->where('product_id', $validated['product_id'])
            ->where('size', $validated['size'])
            ->where('color', $validated['color'])
            ->exists();

        if ($alreadyExists) {
            throw ValidationException::withMessages([
                'size' => 'Varian produk dengan ukuran dan warna tersebut sudah terdaftar.',
            ]);
        }

        DB::transaction(function () use ($validated, $request) {
            $variant = ProductVariant::create([
                'product_id' => $validated['product_id'],
                'size' => $validated['size'],
                'color' => $validated['color'],
                'sku' => $validated['sku'] ?: null,
                'stock_quantity' => $validated['initial_stock'],
                'low_stock_threshold' => $validated['low_stock_threshold'],
            ]);

            if ($validated['initial_stock'] > 0) {
                $variant->movements()->create([
                    'user_id' => $request->user()->id,
                    'type' => 'masuk',
                    'quantity' => $validated['initial_stock'],
                    'stock_before' => 0,
                    'stock_after' => $validated['initial_stock'],
                    'reason' => 'Saldo awal',
                ]);
            }
        });

        return back()->with('success', 'Varian dan stok awal berhasil ditambahkan.');
    }

    public function updateVariant(Request $request, ProductVariant $variant): RedirectResponse
    {
        $validated = $request->validate([
            'low_stock_threshold' => ['required', 'integer', 'min:0', 'max:2000000000'],
        ]);

        $variant->update($validated);

        return back()->with('success', 'Batas stok minimum berhasil diperbarui.');
    }

    public function destroyVariant(ProductVariant $variant): RedirectResponse
    {
        DB::transaction(function () use ($variant) {
            $lockedVariant = ProductVariant::query()->lockForUpdate()->findOrFail($variant->id);
            $lockedVariant->movements()->delete();
            $lockedVariant->delete();
        });

        return back()->with('success', 'Varian stok dan seluruh riwayat pergerakannya berhasil dihapus.');
    }

    public function adjustVariant(Request $request, ProductVariant $variant): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:masuk,keluar'],
            'quantity' => ['required', 'integer', 'min:1', 'max:2000000000'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated, $variant, $request) {
            $lockedVariant = ProductVariant::query()->lockForUpdate()->findOrFail($variant->id);
            $before = $lockedVariant->stock_quantity;
            $quantity = $validated['quantity'];

            if ($validated['type'] === 'keluar' && $quantity > $before) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stok baju tidak mencukupi untuk jumlah barang keluar tersebut.',
                ]);
            }

            $after = $validated['type'] === 'masuk' ? $before + $quantity : $before - $quantity;
            if ($after > 2000000000) {
                throw ValidationException::withMessages([
                    'quantity' => 'Jumlah stok melebihi batas yang didukung.',
                ]);
            }

            $lockedVariant->update(['stock_quantity' => $after]);
            $lockedVariant->movements()->create([
                'user_id' => $request->user()->id,
                'type' => $validated['type'],
                'quantity' => $quantity,
                'stock_before' => $before,
                'stock_after' => $after,
                'reason' => $validated['reason'],
            ]);
        });

        return back()->with('success', 'Pergerakan stok baju berhasil dicatat.');
    }

    public function materials(): View
    {
        return view('employee.inventory.materials', [
            'materials' => RawMaterial::orderBy('name')->paginate(15),
        ]);
    }

    public function storeMaterial(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:30'],
            'initial_stock' => ['required', 'numeric', 'decimal:0,3', 'min:0', 'max:999999999.999'],
            'minimum_stock' => ['required', 'numeric', 'decimal:0,3', 'min:0', 'max:999999999.999'],
            'unit_cost' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($validated, $request) {
            $material = RawMaterial::create([
                'name' => $validated['name'],
                'unit' => $validated['unit'],
                'current_stock' => $validated['initial_stock'],
                'minimum_stock' => $validated['minimum_stock'],
                'unit_cost' => $validated['unit_cost'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            if ((float) $validated['initial_stock'] > 0) {
                $material->movements()->create([
                    'user_id' => $request->user()->id,
                    'type' => 'masuk',
                    'quantity' => $validated['initial_stock'],
                    'stock_before' => 0,
                    'stock_after' => $validated['initial_stock'],
                    'reason' => 'Saldo awal',
                ]);
            }
        });

        return back()->with('success', 'Bahan baku dan stok awal berhasil ditambahkan.');
    }

    public function updateMaterial(Request $request, RawMaterial $material): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:30'],
            'minimum_stock' => ['required', 'numeric', 'decimal:0,3', 'min:0', 'max:999999999.999'],
            'unit_cost' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $material->update($validated);

        return back()->with('success', 'Data bahan baku berhasil diperbarui.');
    }

    public function destroyMaterial(RawMaterial $material): RedirectResponse
    {
        DB::transaction(function () use ($material) {
            $lockedMaterial = RawMaterial::query()->lockForUpdate()->findOrFail($material->id);
            $lockedMaterial->movements()->delete();
            $lockedMaterial->delete();
        });

        return back()->with('success', 'Bahan baku dan seluruh riwayat pergerakannya berhasil dihapus.');
    }

    public function adjustMaterial(Request $request, RawMaterial $material): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:masuk,keluar'],
            'quantity' => ['required', 'numeric', 'decimal:0,3', 'gt:0', 'max:999999999.999'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated, $material, $request) {
            $lockedMaterial = RawMaterial::query()->lockForUpdate()->findOrFail($material->id);
            $before = (float) $lockedMaterial->current_stock;
            $quantity = (float) $validated['quantity'];

            if ($validated['type'] === 'keluar' && $quantity > $before) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stok bahan tidak mencukupi untuk jumlah barang keluar tersebut.',
                ]);
            }

            $after = round($validated['type'] === 'masuk' ? $before + $quantity : $before - $quantity, 3);
            if ($after > 999999999.999) {
                throw ValidationException::withMessages([
                    'quantity' => 'Jumlah stok melebihi batas yang didukung.',
                ]);
            }

            $lockedMaterial->update(['current_stock' => $after]);
            $lockedMaterial->movements()->create([
                'user_id' => $request->user()->id,
                'type' => $validated['type'],
                'quantity' => $quantity,
                'stock_before' => $before,
                'stock_after' => $after,
                'reason' => $validated['reason'],
            ]);
        });

        return back()->with('success', 'Pergerakan stok bahan berhasil dicatat.');
    }

    public function history(): View
    {
        $apparelMovements = ApparelStockMovement::with(['variant.product', 'user'])
            ->latest()
            ->limit(100)
            ->get()
            ->map(fn (ApparelStockMovement $movement) => [
                'created_at' => $movement->created_at,
                'item' => $movement->variant->product->name . ' / ' . $movement->variant->size . ' / ' . $movement->variant->color,
                'category' => 'Baju jadi',
                'type' => $movement->type,
                'quantity' => $movement->quantity,
                'unit' => 'pcs',
                'before' => $movement->stock_before,
                'after' => $movement->stock_after,
                'reason' => $movement->reason,
                'user' => $movement->user?->name ?? 'Akun dihapus',
            ]);

        $materialMovements = MaterialStockMovement::with(['material', 'user'])
            ->latest()
            ->limit(100)
            ->get()
            ->map(fn (MaterialStockMovement $movement) => [
                'created_at' => $movement->created_at,
                'item' => $movement->material->name,
                'category' => 'Bahan baku',
                'type' => $movement->type,
                'quantity' => $movement->quantity,
                'unit' => $movement->material->unit,
                'before' => $movement->stock_before,
                'after' => $movement->stock_after,
                'reason' => $movement->reason,
                'user' => $movement->user?->name ?? 'Akun dihapus',
            ]);

        $movements = Collection::make($apparelMovements)
            ->merge($materialMovements)
            ->sortByDesc('created_at')
            ->take(100);

        return view('employee.inventory.history', compact('movements'));
    }
}
