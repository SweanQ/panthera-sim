@extends('layouts.employee')

@section('title', 'Ringkasan kerja')

@section('content')
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="text-xs font-bold uppercase tracking-[.18em] text-emerald-800">Ruang kerja karyawan</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-stone-900">Ringkasan hari ini</h1>
            <p class="mt-2 text-sm text-stone-600">Pantau produksi dan persediaan Panthera dari satu tempat.</p>
        </div>
        <a href="{{ route('employee.orders.index') }}" class="inline-flex items-center justify-center rounded-lg bg-[#193726] px-4 py-2.5 text-sm font-bold text-white hover:bg-[#244b35]">Buka pesanan produksi</a>
    </div>

    <div class="mt-7 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach([
            ['Pesanan aktif', $stats['active_orders'], 'Pesanan yang belum selesai'],
            ['Varian baju', $stats['apparel_variants'], 'Ukuran dan warna terdaftar'],
            ['Stok baju menipis', $stats['low_apparel'], 'Pada atau di bawah batas minimum'],
            ['Bahan perlu restok', $stats['low_materials'], 'Pada atau di bawah batas minimum'],
        ] as [$label, $value, $hint])
            <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-stone-500">{{ $label }}</p>
                <p class="mt-3 text-3xl font-extrabold text-stone-900">{{ $value }}</p>
                <p class="mt-1 text-xs text-stone-500">{{ $hint }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-7 grid gap-6 xl:grid-cols-2">
        <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-stone-900">Stok baju perlu diperhatikan</h2>
                    <p class="mt-1 text-xs text-stone-500">Varian yang jumlahnya mencapai batas minimum.</p>
                </div>
                <a class="text-xs font-bold text-emerald-800 hover:underline" href="{{ route('inventory.apparel') }}">Kelola stok</a>
            </div>
            <div class="mt-4 divide-y divide-stone-100">
                @forelse($lowApparel as $variant)
                    <div class="flex items-center justify-between gap-3 py-3">
                        <div>
                            <p class="text-sm font-semibold text-stone-800">{{ $variant->product->name }}</p>
                            <p class="text-xs text-stone-500">{{ $variant->size }} / {{ $variant->color }}</p>
                        </div>
                        <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-900">{{ $variant->stock_quantity }} pcs tersisa</span>
                    </div>
                @empty
                    <p class="py-6 text-sm text-stone-500">Belum ada stok baju pada batas minimum.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-stone-900">Bahan baku perlu restok</h2>
                    <p class="mt-1 text-xs text-stone-500">Bahan dengan stok sesuai atau di bawah batas minimum.</p>
                </div>
                <a class="text-xs font-bold text-emerald-800 hover:underline" href="{{ route('inventory.materials') }}">Kelola bahan</a>
            </div>
            <div class="mt-4 divide-y divide-stone-100">
                @forelse($lowMaterials as $material)
                    <div class="flex items-center justify-between gap-3 py-3">
                        <div>
                            <p class="text-sm font-semibold text-stone-800">{{ $material->name }}</p>
                            <p class="text-xs text-stone-500">Batas minimum {{ $material->minimum_stock }} {{ $material->unit }}</p>
                        </div>
                        <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-900">{{ $material->current_stock }} {{ $material->unit }}</span>
                    </div>
                @empty
                    <p class="py-6 text-sm text-stone-500">Belum ada bahan baku pada batas minimum.</p>
                @endforelse
            </div>
        </section>
    </div>

    <section class="mt-7 overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-stone-100 px-5 py-4">
            <div>
                <h2 class="text-lg font-bold text-stone-900">Pesanan terbaru</h2>
                <p class="mt-1 text-xs text-stone-500">Pantau kebutuhan produksi yang baru masuk.</p>
            </div>
            <a class="text-xs font-bold text-emerald-800 hover:underline" href="{{ route('employee.orders.index') }}">Semua pesanan</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-stone-50 text-xs uppercase tracking-wide text-stone-500">
                    <tr><th class="px-5 py-3">Kode</th><th class="px-5 py-3">Pelanggan</th><th class="px-5 py-3">Produk</th><th class="px-5 py-3">Status</th></tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($recentOrders as $order)
                        <tr>
                            <td class="whitespace-nowrap px-5 py-3 font-mono text-xs font-bold text-stone-800">{{ $order->order_code }}</td>
                            <td class="px-5 py-3 text-stone-700">{{ $order->customer?->name ?? 'Pelanggan' }}</td>
                            <td class="px-5 py-3 text-stone-700">{{ $order->product?->name }} <span class="text-xs text-stone-500">({{ $order->quantity }} pcs)</span></td>
                            <td class="whitespace-nowrap px-5 py-3 text-xs font-semibold text-emerald-900">{{ strtoupper(str_replace('_', ' ', $order->production_status)) }}</td>
                        </tr>
                    @empty
                        <tr><td class="px-5 py-8 text-center text-sm text-stone-500" colspan="4">Belum ada pesanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
