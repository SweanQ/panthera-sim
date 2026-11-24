@extends(auth()->user()->role === 'admin' ? 'layouts.panthera' : 'layouts.employee')

@section('title', 'Stok baju')

@section('content')
    @php($isAdmin = auth()->user()->role === 'admin')

    <div>
        <p class="text-xs font-bold uppercase tracking-[.18em] text-emerald-800">Persediaan barang jadi</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-stone-900">Stok baju</h1>
        <p class="mt-2 text-sm text-stone-600">Catat jumlah per produk, ukuran, dan warna. Barang masuk/keluar tidak mengubah stok pesanan secara otomatis.</p>
    </div>

    <section class="mt-6 rounded-2xl border p-5 shadow-sm {{ $isAdmin ? 'border-zinc-800 bg-black text-white' : 'border-stone-200 bg-white' }}">
        <h2 class="text-lg font-bold {{ $isAdmin ? 'text-white' : 'text-stone-900' }}">Tambah varian stok</h2>
        <form action="{{ route('inventory.apparel.store') }}" method="POST" class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @csrf
            <label class="text-xs font-semibold {{ $isAdmin ? 'text-white' : 'text-stone-700' }}">Produk
                <select name="product_id" required class="mt-1.5 w-full rounded-lg text-sm focus:border-emerald-700 focus:ring-emerald-700 {{ $isAdmin ? 'border-zinc-700 bg-black text-white' : 'border-stone-300' }}">
                    <option value="">Pilih produk</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>{{ $product->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="text-xs font-semibold {{ $isAdmin ? 'text-white' : 'text-stone-700' }}">Ukuran
                <input name="size" value="{{ old('size') }}" maxlength="20" required placeholder="S, M, L, XL" class="mt-1.5 w-full rounded-lg text-sm focus:border-emerald-700 focus:ring-emerald-700 {{ $isAdmin ? 'border-zinc-700 bg-black text-white placeholder-zinc-400' : 'border-stone-300' }}">
            </label>
            <label class="text-xs font-semibold {{ $isAdmin ? 'text-white' : 'text-stone-700' }}">Warna
                <input name="color" value="{{ old('color') }}" maxlength="50" required placeholder="Hitam" class="mt-1.5 w-full rounded-lg text-sm focus:border-emerald-700 focus:ring-emerald-700 {{ $isAdmin ? 'border-zinc-700 bg-black text-white placeholder-zinc-400' : 'border-stone-300' }}">
            </label>
            <label class="text-xs font-semibold {{ $isAdmin ? 'text-white' : 'text-stone-700' }}">SKU (opsional)
                <input name="sku" value="{{ old('sku') }}" maxlength="100" class="mt-1.5 w-full rounded-lg text-sm focus:border-emerald-700 focus:ring-emerald-700 {{ $isAdmin ? 'border-zinc-700 bg-black text-white' : 'border-stone-300' }}">
            </label>
            <label class="text-xs font-semibold {{ $isAdmin ? 'text-white' : 'text-stone-700' }}">Stok awal (pcs)
                <input type="number" name="initial_stock" value="{{ old('initial_stock', 0) }}" min="0" max="2000000000" required class="mt-1.5 w-full rounded-lg text-sm focus:border-emerald-700 focus:ring-emerald-700 {{ $isAdmin ? 'border-zinc-700 bg-black text-white' : 'border-stone-300' }}">
            </label>
            <label class="text-xs font-semibold {{ $isAdmin ? 'text-white' : 'text-stone-700' }}">Peringatan minimum (pcs)
                <input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', 0) }}" min="0" max="2000000000" required class="mt-1.5 w-full rounded-lg text-sm focus:border-emerald-700 focus:ring-emerald-700 {{ $isAdmin ? 'border-zinc-700 bg-black text-white' : 'border-stone-300' }}">
            </label>
            <div class="flex items-end">
                <button class="w-full rounded-lg bg-[#193726] px-4 py-2.5 text-sm font-bold text-white hover:bg-[#244b35]" type="submit">Tambah varian</button>
            </div>
        </form>
    </section>

    <section class="mt-6 overflow-hidden rounded-2xl border shadow-sm {{ $isAdmin ? 'border-zinc-800 bg-black text-white' : 'border-stone-200 bg-white' }}">
        <div class="flex items-center justify-between border-b px-5 py-4 {{ $isAdmin ? 'border-zinc-800' : 'border-stone-100' }}">
            <div>
                <h2 class="font-bold {{ $isAdmin ? 'text-white' : 'text-stone-900' }}">Daftar varian</h2>
                <p class="mt-1 text-xs {{ $isAdmin ? 'text-white' : 'text-stone-500' }}">Catat setiap barang masuk atau keluar beserta alasannya.</p>
            </div>
            <a href="{{ route('inventory.history') }}" class="text-xs font-bold {{ $isAdmin ? 'text-white' : 'text-emerald-800' }} hover:underline">Riwayat stok</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm {{ $isAdmin ? 'bg-black text-white' : '' }}">
                <thead class="{{ $isAdmin ? 'bg-black text-xs uppercase tracking-wide text-white' : 'bg-stone-50 text-xs uppercase tracking-wide text-stone-500' }}">
                    <tr><th class="px-5 py-3">Produk / varian</th><th class="px-5 py-3">SKU</th><th class="px-5 py-3">Stok</th><th class="px-5 py-3">Batas minimum</th><th class="px-5 py-3">Catat pergerakan</th></tr>
                </thead>
                <tbody class="{{ $isAdmin ? 'divide-y divide-zinc-800' : 'divide-y divide-stone-100' }}">
                    @forelse($variants as $variant)
                        <tr class="align-top">
                            <td class="px-5 py-4">
                                <p class="font-semibold {{ $isAdmin ? 'text-white' : 'text-stone-900' }}">{{ $variant->product->name }}</p>
                                <p class="mt-1 text-xs {{ $isAdmin ? 'text-white' : 'text-stone-500' }}">{{ $variant->size }} · {{ $variant->color }}</p>
                            </td>
                            <td class="px-5 py-4 text-xs {{ $isAdmin ? 'text-white' : 'text-stone-600' }}">{{ $variant->sku ?: '—' }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $isAdmin ? 'border border-zinc-700 bg-zinc-900 text-white' : ($variant->stock_quantity <= $variant->low_stock_threshold ? 'bg-amber-50 text-amber-900' : 'bg-emerald-50 text-emerald-900') }}">{{ $variant->stock_quantity }} pcs</span>
                            </td>
                            <td class="px-5 py-4">
                                <form action="{{ route('inventory.apparel.threshold', $variant) }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="low_stock_threshold" value="{{ $variant->low_stock_threshold }}" min="0" max="2000000000" required aria-label="Batas minimum stok" class="w-24 rounded-lg text-xs focus:border-emerald-700 focus:ring-emerald-700 {{ $isAdmin ? 'border-zinc-700 bg-black text-white' : 'border-stone-300' }}">
                                    <button class="rounded-lg border px-2.5 py-2 text-xs font-bold {{ $isAdmin ? 'border-zinc-700 text-white hover:bg-zinc-900' : 'border-stone-300 text-stone-700 hover:bg-stone-50' }}" type="submit">Simpan</button>
                                </form>
                            </td>
                            <td class="px-5 py-4">
                                <form action="{{ route('inventory.apparel.adjust', $variant) }}" method="POST" class="grid min-w-64 grid-cols-2 gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="type" class="rounded-lg text-xs focus:border-emerald-700 focus:ring-emerald-700 {{ $isAdmin ? 'border-zinc-700 bg-black text-white' : 'border-stone-300' }}">
                                        <option value="masuk">Barang masuk</option>
                                        <option value="keluar">Barang keluar</option>
                                    </select>
                                    <input type="number" name="quantity" min="1" max="2000000000" required placeholder="Jumlah pcs" class="rounded-lg text-xs focus:border-emerald-700 focus:ring-emerald-700 {{ $isAdmin ? 'border-zinc-700 bg-black text-white placeholder-zinc-400' : 'border-stone-300' }}">
                                    <input name="reason" maxlength="255" required placeholder="Alasan pergerakan stok" class="col-span-2 rounded-lg text-xs focus:border-emerald-700 focus:ring-emerald-700 {{ $isAdmin ? 'border-zinc-700 bg-black text-white placeholder-zinc-400' : 'border-stone-300' }}">
                                    <button class="col-span-2 rounded-lg bg-emerald-800 px-3 py-2 text-xs font-bold text-white hover:bg-emerald-900" type="submit">Catat stok</button>
                                </form>
                                <details class="mt-3">
                                    <summary class="cursor-pointer rounded-lg border px-3 py-2 text-center text-xs font-bold {{ $isAdmin ? 'border-rose-800 text-rose-300 hover:bg-zinc-900' : 'border-rose-300 text-rose-800 hover:bg-rose-50' }}">Hapus varian</summary>
                                    <div class="mt-2 rounded-lg border p-3 {{ $isAdmin ? 'border-zinc-700 bg-black text-white' : 'border-stone-200 bg-white text-stone-700' }}">
                                        <p class="mb-2 text-xs">Penghapusan juga menghapus seluruh riwayat pergerakan stok varian ini.</p>
                                        <form action="{{ route('inventory.apparel.destroy', $variant) }}" method="POST" onsubmit="return confirm('Hapus varian stok ini beserta semua riwayat pergerakannya?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="w-full rounded-lg bg-rose-700 px-3 py-2 text-xs font-bold text-white hover:bg-rose-800" type="submit">Konfirmasi hapus varian</button>
                                        </form>
                                    </div>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="px-5 py-10 text-center text-sm {{ $isAdmin ? 'text-white' : 'text-stone-500' }}" colspan="5">Belum ada varian stok. Tambahkan produk katalog dan varian pertamanya.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($variants->hasPages())
            <div class="border-t px-5 py-4 {{ $isAdmin ? 'border-zinc-800' : 'border-stone-100' }}">{{ $variants->links() }}</div>
        @endif
    </section>
@endsection
