@extends(auth()->user()->role === 'admin' ? 'layouts.panthera' : 'layouts.employee')

@section('title', 'Stok bahan')

@section('content')
    @php($isAdmin = auth()->user()->role === 'admin')

    <div>
        <p class="text-xs font-bold uppercase tracking-[.18em] text-emerald-800">Persediaan bahan baku</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-stone-900">Stok bahan</h1>
        <p class="mt-2 text-sm text-stone-600">Simpan jumlah, satuan, biaya, batas minimum, dan catatan untuk setiap bahan.</p>
    </div>

    <section class="mt-6 rounded-2xl border p-5 shadow-sm {{ $isAdmin ? 'border-zinc-800 bg-black text-white' : 'border-stone-200 bg-white' }}">
        <h2 class="text-lg font-bold {{ $isAdmin ? 'text-white' : 'text-stone-900' }}">Tambah bahan baku</h2>
        <form action="{{ route('inventory.materials.store') }}" method="POST" class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @csrf
            <label class="text-xs font-semibold {{ $isAdmin ? 'text-white' : 'text-stone-700' }}">Nama bahan
                <input name="name" value="{{ old('name') }}" maxlength="255" required placeholder="Cotton Combed 24s" class="mt-1.5 w-full rounded-lg text-sm focus:border-emerald-700 focus:ring-emerald-700 {{ $isAdmin ? 'border-zinc-700 bg-black text-white placeholder-zinc-400' : 'border-stone-300' }}">
            </label>
            <label class="text-xs font-semibold {{ $isAdmin ? 'text-white' : 'text-stone-700' }}">Satuan
                <input name="unit" value="{{ old('unit') }}" maxlength="30" required placeholder="meter, kg, roll, pcs" class="mt-1.5 w-full rounded-lg text-sm focus:border-emerald-700 focus:ring-emerald-700 {{ $isAdmin ? 'border-zinc-700 bg-black text-white placeholder-zinc-400' : 'border-stone-300' }}">
            </label>
            <label class="text-xs font-semibold {{ $isAdmin ? 'text-white' : 'text-stone-700' }}">Stok awal
                <input type="number" name="initial_stock" value="{{ old('initial_stock', 0) }}" min="0" max="999999999.999" step="0.001" required class="mt-1.5 w-full rounded-lg text-sm focus:border-emerald-700 focus:ring-emerald-700 {{ $isAdmin ? 'border-zinc-700 bg-black text-white' : 'border-stone-300' }}">
            </label>
            <label class="text-xs font-semibold {{ $isAdmin ? 'text-white' : 'text-stone-700' }}">Batas minimum
                <input type="number" name="minimum_stock" value="{{ old('minimum_stock', 0) }}" min="0" max="999999999.999" step="0.001" required class="mt-1.5 w-full rounded-lg text-sm focus:border-emerald-700 focus:ring-emerald-700 {{ $isAdmin ? 'border-zinc-700 bg-black text-white' : 'border-stone-300' }}">
            </label>
            <label class="text-xs font-semibold {{ $isAdmin ? 'text-white' : 'text-stone-700' }}">Biaya per satuan (opsional)
                <input type="number" name="unit_cost" value="{{ old('unit_cost') }}" min="0" step="0.01" class="mt-1.5 w-full rounded-lg text-sm focus:border-emerald-700 focus:ring-emerald-700 {{ $isAdmin ? 'border-zinc-700 bg-black text-white' : 'border-stone-300' }}">
            </label>
            <label class="text-xs font-semibold {{ $isAdmin ? 'text-white' : 'text-stone-700' }}">Catatan (opsional)
                <input name="notes" value="{{ old('notes') }}" maxlength="1000" class="mt-1.5 w-full rounded-lg text-sm focus:border-emerald-700 focus:ring-emerald-700 {{ $isAdmin ? 'border-zinc-700 bg-black text-white' : 'border-stone-300' }}">
            </label>
            <div class="sm:col-span-2 xl:col-span-3">
                <button class="rounded-lg bg-[#193726] px-4 py-2.5 text-sm font-bold text-white hover:bg-[#244b35]" type="submit">Tambah bahan</button>
            </div>
        </form>
    </section>

    <section class="mt-6 grid gap-4 xl:grid-cols-2">
        @forelse($materials as $material)
            <article class="rounded-2xl border p-5 shadow-sm {{ $isAdmin ? 'border-zinc-800 bg-black text-white' : 'border-stone-200 bg-white' }}">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-bold {{ $isAdmin ? 'text-white' : 'text-stone-900' }}">{{ $material->name }}</h2>
                        <p class="mt-1 text-xs {{ $isAdmin ? 'text-white' : 'text-stone-500' }}">{{ $material->unit_cost ? 'Rp ' . number_format((float) $material->unit_cost, 2, ',', '.') . ' / ' . $material->unit : 'Biaya belum dicatat' }}</p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $isAdmin ? 'border border-zinc-700 bg-zinc-900 text-white' : ((float) $material->current_stock <= (float) $material->minimum_stock ? 'bg-amber-50 text-amber-900' : 'bg-emerald-50 text-emerald-900') }}">
                        {{ $material->current_stock }} {{ $material->unit }}
                    </span>
                </div>
                @if($material->notes)
                    <p class="mt-3 text-xs {{ $isAdmin ? 'text-white' : 'text-stone-600' }}">{{ $material->notes }}</p>
                @endif

                <details class="mt-4 rounded-lg border p-3 {{ $isAdmin ? 'border-zinc-700' : 'border-stone-200' }}">
                    <summary class="cursor-pointer text-xs font-bold {{ $isAdmin ? 'text-white' : 'text-stone-700' }}">Ubah detail bahan</summary>
                    <form action="{{ route('inventory.materials.update', $material) }}" method="POST" class="mt-3 grid gap-3 sm:grid-cols-2">
                        @csrf
                        @method('PATCH')
                        <label class="text-xs font-semibold {{ $isAdmin ? 'text-white' : 'text-stone-600' }}">Nama
                            <input name="name" value="{{ $material->name }}" maxlength="255" required class="mt-1 w-full rounded-lg text-sm {{ $isAdmin ? 'border-zinc-700 bg-black text-white' : 'border-stone-300' }}">
                        </label>
                        <label class="text-xs font-semibold {{ $isAdmin ? 'text-white' : 'text-stone-600' }}">Satuan
                            <input name="unit" value="{{ $material->unit }}" maxlength="30" required class="mt-1 w-full rounded-lg text-sm {{ $isAdmin ? 'border-zinc-700 bg-black text-white' : 'border-stone-300' }}">
                        </label>
                        <label class="text-xs font-semibold {{ $isAdmin ? 'text-white' : 'text-stone-600' }}">Batas minimum
                            <input type="number" name="minimum_stock" value="{{ $material->minimum_stock }}" min="0" step="0.001" required class="mt-1 w-full rounded-lg text-sm {{ $isAdmin ? 'border-zinc-700 bg-black text-white' : 'border-stone-300' }}">
                        </label>
                        <label class="text-xs font-semibold {{ $isAdmin ? 'text-white' : 'text-stone-600' }}">Biaya per satuan
                            <input type="number" name="unit_cost" value="{{ $material->unit_cost }}" min="0" step="0.01" class="mt-1 w-full rounded-lg text-sm {{ $isAdmin ? 'border-zinc-700 bg-black text-white' : 'border-stone-300' }}">
                        </label>
                        <label class="text-xs font-semibold {{ $isAdmin ? 'text-white' : 'text-stone-600' }} sm:col-span-2">Catatan
                            <input name="notes" value="{{ $material->notes }}" maxlength="1000" class="mt-1 w-full rounded-lg text-sm {{ $isAdmin ? 'border-zinc-700 bg-black text-white' : 'border-stone-300' }}">
                        </label>
                        <button class="rounded-lg border px-3 py-2 text-xs font-bold sm:col-span-2 {{ $isAdmin ? 'border-zinc-700 text-white hover:bg-zinc-900' : 'border-stone-300 text-stone-700 hover:bg-stone-50' }}" type="submit">Simpan detail</button>
                    </form>
                </details>

                <form action="{{ route('inventory.materials.adjust', $material) }}" method="POST" class="mt-4 grid gap-2 sm:grid-cols-[1fr_1fr_2fr_auto]">
                    @csrf
                    @method('PATCH')
                    <select name="type" class="rounded-lg text-xs {{ $isAdmin ? 'border-zinc-700 bg-black text-white' : 'border-stone-300' }}">
                        <option value="masuk">Bahan masuk</option>
                        <option value="keluar">Bahan keluar</option>
                    </select>
                    <input type="number" name="quantity" min="0.001" step="0.001" max="999999999.999" required placeholder="Jumlah {{ $material->unit }}" class="rounded-lg text-xs {{ $isAdmin ? 'border-zinc-700 bg-black text-white placeholder-zinc-400' : 'border-stone-300' }}">
                    <input name="reason" maxlength="255" required placeholder="Alasan pergerakan stok" class="rounded-lg text-xs {{ $isAdmin ? 'border-zinc-700 bg-black text-white placeholder-zinc-400' : 'border-stone-300' }}">
                    <button class="rounded-lg bg-emerald-800 px-3 py-2 text-xs font-bold text-white hover:bg-emerald-900" type="submit">Catat</button>
                </form>

                <details class="mt-4">
                    <summary class="cursor-pointer rounded-lg border px-3 py-2 text-center text-xs font-bold {{ $isAdmin ? 'border-rose-800 text-rose-300 hover:bg-zinc-900' : 'border-rose-300 text-rose-800 hover:bg-rose-50' }}">Hapus bahan</summary>
                    <div class="mt-2 rounded-lg border p-3 {{ $isAdmin ? 'border-zinc-700 bg-black text-white' : 'border-stone-200 bg-white text-stone-700' }}">
                        <p class="mb-2 text-xs">Penghapusan juga menghapus seluruh riwayat pergerakan stok bahan ini.</p>
                        <form action="{{ route('inventory.materials.destroy', $material) }}" method="POST" onsubmit="return confirm('Hapus bahan baku ini beserta semua riwayat pergerakannya?')">
                            @csrf
                            @method('DELETE')
                            <button class="w-full rounded-lg bg-rose-700 px-3 py-2 text-xs font-bold text-white hover:bg-rose-800" type="submit">Konfirmasi hapus bahan</button>
                        </form>
                    </div>
                </details>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed p-8 text-center text-sm xl:col-span-2 {{ $isAdmin ? 'border-zinc-700 bg-black text-white' : 'border-stone-300 bg-white text-stone-500' }}">Belum ada bahan baku yang dicatat.</div>
        @endforelse
    </section>
    @if($materials->hasPages())
        <div class="mt-5 {{ $isAdmin ? 'text-white' : '' }}">{{ $materials->links() }}</div>
    @endif
@endsection
