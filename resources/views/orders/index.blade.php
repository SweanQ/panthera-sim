@extends('layouts.panthera')

@section('content')
<div class="space-y-8">
    <!-- Header & Primary Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-zinc-900">Dashboard Operasional</h1>
            <p class="text-sm text-stone-600 mt-1">Pantau pesanan Panthera, revisi desain, dan progres produksi.</p>
        </div>

        @if(auth()->user()->role === 'admin')
            <div class="flex items-center gap-3">
                <a href="{{ route('reports.index') }}" class="px-4 py-2.5 rounded-lg text-sm font-medium bg-zinc-900 hover:bg-zinc-800 text-zinc-200 border border-zinc-800 transition">
                    Laporan Keuangan
                </a>
                <a href="{{ route('orders.create') }}" class="px-4 py-2.5 rounded-lg text-sm font-semibold border border-zinc-900 bg-transparent hover:bg-zinc-100 text-zinc-900 transition">
                    + Catat Pesanan Baru
                </a>
            </div>
        @endif
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="p-4 rounded-lg bg-emerald-950/60 border border-emerald-800/80 text-emerald-300 text-sm flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white text-xs">Tutup</button>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-lg bg-red-950/60 border border-red-800/80 text-red-300 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="p-5 rounded-xl bg-black border border-zinc-800">
            <div class="text-xs font-medium uppercase tracking-wider text-white">Total Pesanan</div>
            <div class="text-3xl font-semibold text-white mt-2">{{ $stats['total_orders'] }}</div>
        </div>
        <div class="p-5 rounded-xl bg-black border border-zinc-800">
            <div class="text-xs font-medium uppercase tracking-wider text-white">Dalam Produksi</div>
            <div class="text-3xl font-semibold text-amber-400 mt-2">{{ $stats['in_production'] }}</div>
        </div>
        <div class="p-5 rounded-xl bg-black border border-zinc-800">
            <div class="text-xs font-medium uppercase tracking-wider text-white">Selesai / Diambil</div>
            <div class="text-3xl font-semibold text-emerald-400 mt-2">{{ $stats['completed'] }}</div>
        </div>
        <div class="p-5 rounded-xl bg-black border border-zinc-800">
            <div class="text-xs font-medium uppercase tracking-wider text-white">Dibatalkan</div>
            <div class="text-3xl font-semibold text-rose-400 mt-2">{{ $stats['cancelled_orders'] }}</div>
        </div>
        @if(auth()->user()->role === 'admin')
            <div class="p-5 rounded-xl bg-black border border-zinc-800">
                <div class="text-xs font-medium uppercase tracking-wider text-white">Total Kas Masuk</div>
                <div class="text-2xl font-semibold text-white mt-2">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</div>
            </div>
        @endif
    </div>

    <!-- Orders Table Card -->
    <div class="rounded-xl bg-black border border-zinc-800 overflow-hidden">
        <div class="p-5 border-b border-zinc-800/90 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="text-base font-semibold text-white">Daftar Pesanan & Pemantauan Produksi</h2>
            <input type="text" id="searchOrder" onkeyup="filterTable()" placeholder="Cari kode atau pelanggan..."
                   class="px-3.5 py-2 rounded-lg bg-black border border-zinc-800 text-sm text-white placeholder-zinc-300 focus:outline-none focus:border-zinc-600 w-full sm:w-64">
        </div>

        <div class="orders-table-scroll overflow-x-auto">
            <table id="ordersTable" class="min-w-full divide-y divide-zinc-800/90 text-sm">
                <thead class="bg-black text-white text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5 text-left font-medium">Kode</th>
                        <th class="px-5 py-3.5 text-left font-medium">Pelanggan</th>
                        <th class="px-5 py-3.5 text-left font-medium">Produk & Spesifikasi</th>
                        <th class="px-5 py-3.5 text-left font-medium">Pembayaran</th>
                        <th class="px-5 py-3.5 text-left font-medium">Revisi</th>
                        <th class="px-5 py-3.5 text-left font-medium">Status Produksi</th>
                        <th class="px-5 py-3.5 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/70">
                    @if($orders->count() > 0)
                        @foreach($orders as $order)
                        <tr class="hover:bg-zinc-800/40 transition">
                            <td class="px-5 py-4 font-mono text-xs font-semibold text-white whitespace-nowrap">
                                {{ $order->order_code }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-medium text-white">{{ $order->customer?->name ?? 'Pelanggan' }}</div>
                                <div class="text-xs text-white">{{ $order->customer?->whatsapp_number }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-white">{{ $order->product?->name }} <span class="text-white">({{ $order->quantity }} pcs)</span></div>
                                <div class="text-xs text-white mt-0.5">
                                    Size {{ $order->size }} &bull; {{ $order->color }} &bull; Stok {{ strtoupper($order->product?->stock_source ?? '-') }}
                                </div>
                                @if($order->notes)
                                    <div class="text-xs text-white mt-1">Catatan: {{ $order->notes }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 text-[11px] font-semibold rounded-full {{ $order->payment_status === 'lunas' ? 'bg-emerald-950 text-emerald-300 border border-emerald-800' : 'bg-amber-950 text-amber-300 border border-amber-800' }}">
                                    {{ strtoupper(str_replace('_', ' ', $order->payment_status)) }}
                                </span>
                                <div class="text-xs text-white mt-1">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="text-zinc-300 font-mono text-xs">{{ $order->revision_count }}/3</span>
                                @if(auth()->user()->role === 'admin' && $order->revision_count < 3 && $order->production_status !== 'dibatalkan')
                                    <form action="{{ route('orders.revision', $order) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="ml-1.5 px-2 py-0.5 text-[11px] rounded bg-zinc-800 hover:bg-zinc-700 text-zinc-200 border border-zinc-700 transition">
                                            +1
                                        </button>
                                    </form>
                                @elseif($order->revision_count >= 3)
                                    <span class="ml-1 text-[10px] text-zinc-500 uppercase">Max</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($order->production_status === 'dibatalkan')
                                    <span class="text-xs font-semibold text-rose-300">Dibatalkan</span>
                                    <div class="mt-1 max-w-xs whitespace-normal text-[11px] text-white">{{ $order->cancellation_reason }}</div>
                                    <div class="mt-1 text-[10px] text-white">{{ $order->cancelled_at?->format('d/m/Y H:i') }} · {{ $order->cancelledBy?->name ?? 'Akun dihapus' }}</div>
                                @else
                                    <form action="{{ route('orders.updateStatus', $order) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <select name="production_status" class="text-xs bg-zinc-950 border border-zinc-700 text-zinc-200 rounded-md px-2.5 py-1.5 focus:outline-none focus:border-white">
                                            @foreach(['konsultasi', 'desain_disetujui', 'proses_sablon', 'selesai', 'diambil'] as $status)
                                                <option value="{{ $status }}" {{ $order->production_status === $status ? 'selected' : '' }}>
                                                    {{ strtoupper(str_replace('_', ' ', $status)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="px-2.5 py-1.5 rounded-md bg-zinc-800 hover:bg-white hover:text-black text-zinc-200 text-xs font-medium border border-zinc-700 transition">
                                            Simpan
                                        </button>
                                    </form>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-right">
                                @if(auth()->user()->role === 'admin')
                                    <div class="inline-flex items-center gap-2">
                                        @if($order->payment_status !== 'lunas' && $order->production_status !== 'dibatalkan')
                                            <form action="{{ route('orders.settle', $order) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-2.5 py-1.5 rounded-md bg-zinc-900 hover:bg-zinc-800 text-zinc-300 border border-zinc-700 text-xs transition">
                                                    Lunasi
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('orders.invoice', $order) }}" target="_blank"
                                           class="px-3 py-1.5 rounded-md bg-white hover:bg-zinc-200 text-black text-xs font-semibold transition">
                                            Invoice PDF
                                        </a>
                                        @if($order->canBeCancelled())
                                            <details class="text-left">
                                                <summary class="cursor-pointer rounded-md border border-rose-900 px-2.5 py-1.5 text-xs font-semibold text-rose-300 hover:bg-rose-950/50">Batalkan</summary>
                                                <form action="{{ route('orders.cancel', $order) }}" method="POST" class="fixed inset-x-4 bottom-4 z-50 mx-auto max-h-[calc(100vh-2rem)] w-auto max-w-md overflow-y-auto rounded-lg border border-zinc-700 bg-zinc-950 p-4 shadow-2xl sm:left-auto sm:right-4 sm:mx-0 sm:w-80">
                                                    @csrf
                                                    @method('PATCH')
                                                    <label class="block break-words text-left text-xs text-zinc-300">Alasan pembatalan
                                                        <textarea name="reason" required maxlength="500" rows="3" class="mt-1 block w-full min-w-0 rounded-md border-zinc-700 bg-zinc-900 text-xs text-white"></textarea>
                                                    </label>
                                                    <p class="mt-2 text-[10px] text-amber-300">Pembayaran yang sudah tercatat tidak dihapus atau dikembalikan otomatis.</p>
                                                    <button class="mt-2 w-full rounded-md bg-rose-700 px-3 py-2 text-xs font-bold text-white hover:bg-rose-600" type="submit">Konfirmasi pembatalan</button>
                                                </form>
                                            </details>
                                        @endif
                                        <details class="text-left">
                                            <summary class="cursor-pointer rounded-md border border-rose-900 px-2.5 py-1.5 text-xs font-semibold text-rose-300 hover:bg-rose-950/50">Hapus permanen</summary>
                                            <form action="{{ route('orders.destroy', $order) }}" method="POST" role="dialog" aria-modal="true" aria-label="Konfirmasi hapus permanen" class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto bg-black/70 p-4 backdrop-blur-sm">
                                                @csrf
                                                @method('DELETE')
                                                <div class="box-border max-h-[calc(100vh-2rem)] w-full min-w-0 max-w-md overflow-x-hidden overflow-y-auto rounded-xl border border-rose-900 bg-zinc-950 p-5 shadow-2xl">
                                                    <p class="break-words text-left text-sm leading-5 text-zinc-300">Hapus pesanan dan pembayarannya permanen?</p>
                                                    <input name="confirmation" required autocomplete="off" placeholder="{{ $order->order_code }}" class="mt-4 block w-full min-w-0 max-w-full rounded-md border-zinc-700 bg-zinc-900 text-sm text-white">
                                                    <p class="mt-2 break-words text-left text-xs text-zinc-400">Ketik kode pesanan untuk mengonfirmasi.</p>
                                                    <div class="mt-4 flex justify-end gap-2">
                                                        <button class="rounded-md border border-zinc-700 px-3 py-2 text-xs font-semibold text-zinc-300 hover:bg-zinc-900" type="button" onclick="this.closest('details').open = false">Batal</button>
                                                        <button class="rounded-md bg-rose-700 px-3 py-2 text-xs font-bold text-white hover:bg-rose-600" type="submit">Hapus permanen</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </details>
                                    </div>
                                @else
                                    <span class="text-xs text-white">Mode Produksi</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <div class="text-white text-sm">Belum ada pesanan tercatat.</div>
                                @if(auth()->user()->role === 'admin')
                                    <a href="{{ route('orders.create') }}" class="inline-block mt-3 px-4 py-2 rounded-lg bg-white text-black text-xs font-semibold hover:bg-zinc-200 transition">
                                        + Buat Pesanan Pertama
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filterTable() {
    const query = document.getElementById('searchOrder').value.toLowerCase();
    const rows = document.querySelectorAll('#ordersTable tbody tr');
    rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(query) ? '' : 'none';
    });
}
</script>
@endsection