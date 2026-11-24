@extends(auth()->user()->role === 'admin' ? 'layouts.panthera' : 'layouts.employee')

@section('title', 'Katalog produk')

@section('content')
    <div>
        <p class="text-xs font-bold uppercase tracking-[.18em] text-emerald-800">Data produk</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-stone-900">Katalog produk</h1>
        <p class="mt-2 text-sm text-stone-600">Arsipkan produk untuk menyembunyikannya dari katalog. Hapus permanen hanya tersedia jika belum ada pesanan atau varian stok.</p>
    </div>

    <section class="mt-6 rounded-2xl border border-zinc-800 bg-black p-5 text-white shadow-sm">
        <h2 class="text-lg font-bold text-white">Tambah produk</h2>
        <form action="{{ route('inventory.catalog.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @csrf
            <label class="text-xs font-semibold text-white">Nama produk
                <input name="name" value="{{ old('name') }}" maxlength="255" required class="mt-1.5 w-full rounded-lg border-zinc-700 bg-black text-sm text-white focus:border-emerald-700 focus:ring-emerald-700">
            </label>
            <label class="text-xs font-semibold text-white">Material / deskripsi
                <input name="material" value="{{ old('material') }}" maxlength="255" class="mt-1.5 w-full rounded-lg border-zinc-700 bg-black text-sm text-white focus:border-emerald-700 focus:ring-emerald-700">
            </label>
            <label class="text-xs font-semibold text-white">Sumber stok
                <select name="stock_source" required class="mt-1.5 w-full rounded-lg border-zinc-700 bg-black text-sm text-white focus:border-emerald-700 focus:ring-emerald-700">
                    <option value="mandiri" @selected(old('stock_source') === 'mandiri')>Mandiri</option>
                    <option value="jakarta" @selected(old('stock_source') === 'jakarta')>Jakarta</option>
                </select>
            </label>
            <label class="text-xs font-semibold text-white">Harga dasar (Rp)
                <input type="number" name="base_price" value="{{ old('base_price') }}" min="0" max="9999999999.99" step="0.01" required class="mt-1.5 w-full rounded-lg border-zinc-700 bg-black text-sm text-white focus:border-emerald-700 focus:ring-emerald-700">
            </label>
            <label class="text-xs font-semibold text-white">Foto produk (opsional, maks. 5 MB)
                <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" class="mt-1.5 block w-full rounded-lg border border-zinc-700 bg-black text-sm text-white file:mr-3 file:border-0 file:bg-zinc-900 file:px-3 file:py-2 file:text-white">
                @error('image')
                    <span class="mt-1 block text-xs text-rose-300">{{ $message }}</span>
                @enderror
            </label>
            <div class="sm:col-span-2 xl:col-span-4">
                <button class="rounded-lg bg-[#193726] px-4 py-2.5 text-sm font-bold text-white hover:bg-[#244b35]" type="submit">Tambah ke katalog</button>
            </div>
        </form>
    </section>

    <section class="mt-6 space-y-3">
        @forelse($products as $product)
            <article class="rounded-2xl border border-zinc-800 bg-black p-5 text-white shadow-sm">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-3">
                        @if($product->image_path)
                            <img src="{{ asset('storage/' . $product->image_path) }}" alt="Foto {{ $product->name }}" loading="lazy" class="h-20 w-20 rounded-lg object-cover">
                        @else
                            <div class="flex h-20 w-20 items-center justify-center rounded-lg bg-zinc-900 text-center text-[10px] text-white">Foto belum tersedia</div>
                        @endif
                        <div>
                        <h2 class="font-bold text-white">{{ $product->name }}</h2>
                        <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-white">
                            <span>{{ $product->variants_count }} varian stok · {{ $product->orders_count }} pesanan · sumber {{ strtoupper($product->stock_source) }}</span>
                            <span class="rounded-full border border-zinc-700 bg-zinc-900 px-2 py-0.5 font-bold text-white">{{ $product->is_active ? 'Aktif' : 'Diarsipkan' }}</span>
                        </div>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-white">Rp {{ number_format((float) $product->base_price, 0, ',', '.') }}</span>
                </div>
                <form action="{{ route('inventory.catalog.update', $product) }}" method="POST" enctype="multipart/form-data" class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    @csrf
                    @method('PATCH')
                    <label class="text-xs font-semibold text-white">Nama produk
                        <input name="name" value="{{ $product->name }}" maxlength="255" required class="mt-1 w-full rounded-lg border-zinc-700 bg-black text-sm text-white">
                    </label>
                    <label class="text-xs font-semibold text-white">Material / deskripsi
                        <input name="material" value="{{ $product->material }}" maxlength="255" class="mt-1 w-full rounded-lg border-zinc-700 bg-black text-sm text-white">
                    </label>
                    <label class="text-xs font-semibold text-white">Sumber stok
                        <select name="stock_source" required class="mt-1 w-full rounded-lg border-zinc-700 bg-black text-sm text-white">
                            <option value="mandiri" @selected($product->stock_source === 'mandiri')>Mandiri</option>
                            <option value="jakarta" @selected($product->stock_source === 'jakarta')>Jakarta</option>
                        </select>
                    </label>
                    <label class="text-xs font-semibold text-white">Harga dasar (Rp)
                        <input type="number" name="base_price" value="{{ $product->base_price }}" min="0" max="9999999999.99" step="0.01" required class="mt-1 w-full rounded-lg border-zinc-700 bg-black text-sm text-white">
                    </label>
                    <label class="text-xs font-semibold text-white">Ganti foto produk (opsional, maks. 5 MB)
                        <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" class="mt-1 block w-full rounded-lg border border-zinc-700 bg-black text-sm text-white file:mr-3 file:border-0 file:bg-zinc-900 file:px-3 file:py-2 file:text-white">
                        @error('image')
                            <span class="mt-1 block text-xs text-rose-300">{{ $message }}</span>
                        @enderror
                    </label>
                    <button class="w-fit justify-self-start rounded-lg border border-zinc-700 px-2 py-1.5 text-xs font-bold text-white hover:bg-zinc-900" type="submit">Simpan perubahan produk</button>
                </form>
                <div class="mt-4 flex flex-wrap items-center gap-3 border-t border-zinc-800 pt-4">
                    @if($product->is_active)
                        <form action="{{ route('inventory.catalog.archive', $product) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button class="rounded-lg border border-amber-700 px-3 py-2 text-xs font-bold text-amber-200 hover:bg-zinc-900" type="submit">Arsipkan produk</button>
                        </form>
                    @else
                        <form action="{{ route('inventory.catalog.restore', $product) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button class="rounded-lg border border-emerald-700 px-3 py-2 text-xs font-bold text-emerald-200 hover:bg-zinc-900" type="submit">Pulihkan ke katalog</button>
                        </form>
                    @endif

                    @if($product->orders_count === 0 && $product->variants_count === 0)
                        <details>
                            <summary class="cursor-pointer rounded-lg border border-rose-800 px-3 py-2 text-xs font-bold text-rose-300 hover:bg-zinc-900">Hapus permanen</summary>
                            <form action="{{ route('inventory.catalog.destroy', $product) }}" method="POST" class="mt-3 max-w-md space-y-2 rounded-lg border border-zinc-700 bg-black p-3">
                                @csrf
                                @method('DELETE')
                                <label class="block text-xs font-semibold text-white">Ketik nama produk untuk konfirmasi
                                    <input name="confirmation" required autocomplete="off" placeholder="{{ $product->name }}" class="mt-1 block w-full min-w-0 rounded-lg border-zinc-700 bg-black text-sm text-white placeholder-zinc-400">
                                </label>
                                <button class="rounded-lg bg-rose-700 px-3 py-2 text-xs font-bold text-white hover:bg-rose-800" type="submit">Konfirmasi hapus</button>
                            </form>
                        </details>
                    @else
                        <span class="text-xs text-white">Hapus permanen tidak tersedia karena ada pesanan atau data stok.</span>
                    @endif
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-zinc-700 bg-black p-8 text-center text-sm text-white">Belum ada produk katalog.</div>
        @endforelse
    </section>
    @if($products->hasPages())
        <div class="mt-5">{{ $products->links() }}</div>
    @endif
@endsection
