@extends('layouts.employee')

@section('title', 'Pesanan produksi')

@section('content')
    <div>
        <p class="text-xs font-bold uppercase tracking-[.18em] text-emerald-800">Produksi</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-stone-900">Pesanan produksi</h1>
        <p class="mt-2 text-sm text-stone-600">Perbarui tahap pengerjaan pesanan. Pembayaran dan laporan keuangan dikelola admin.</p>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-stone-50 text-xs uppercase tracking-wide text-stone-500">
                    <tr>
                        <th class="px-5 py-3">Pesanan</th>
                        <th class="px-5 py-3">Pelanggan</th>
                        <th class="px-5 py-3">Produk / spesifikasi</th>
                        <th class="px-5 py-3">Status produksi</th>
                        <th class="px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($orders as $order)
                        <tr class="align-top">
                            <td class="whitespace-nowrap px-5 py-4">
                                <p class="font-mono text-xs font-bold text-stone-900">{{ $order->order_code }}</p>
                                <p class="mt-1 text-xs text-stone-500">{{ $order->created_at->format('d/m/Y') }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-stone-800">{{ $order->customer?->name ?? 'Pelanggan' }}</p>
                                <p class="text-xs text-stone-500">{{ $order->customer?->whatsapp_number }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-stone-800">{{ $order->product?->name }}</p>
                                <p class="mt-1 text-xs text-stone-500">{{ $order->quantity }} pcs · {{ $order->size }} · {{ $order->color }}</p>
                                @if($order->notes)
                                    <p class="mt-2 max-w-sm text-xs text-stone-600">{{ $order->notes }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $order->production_status === 'dibatalkan' ? 'bg-rose-50 text-rose-900' : 'bg-emerald-50 text-emerald-900' }}">{{ strtoupper(str_replace('_', ' ', $order->production_status)) }}</span>
                                @if($order->production_status === 'dibatalkan')
                                    <p class="mt-2 max-w-xs text-xs text-stone-600">Alasan: {{ $order->cancellation_reason }}</p>
                                    <p class="mt-1 text-[11px] text-stone-500">{{ $order->cancelled_at?->format('d/m/Y H:i') }} · {{ $order->cancelledBy?->name ?? 'Akun dihapus' }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if($order->production_status !== 'dibatalkan')
                                    <form action="{{ route('employee.orders.updateStatus', $order) }}" method="POST" class="flex min-w-56 flex-col gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <select name="production_status" class="rounded-lg border-stone-300 text-xs focus:border-emerald-700 focus:ring-emerald-700">
                                            @foreach(['konsultasi', 'desain_disetujui', 'proses_sablon', 'selesai', 'diambil'] as $status)
                                                <option value="{{ $status }}" @selected($order->production_status === $status)>{{ strtoupper(str_replace('_', ' ', $status)) }}</option>
                                            @endforeach
                                        </select>
                                        <button class="rounded-lg bg-[#193726] px-3 py-2 text-xs font-bold text-white hover:bg-[#244b35]" type="submit">Simpan status</button>
                                    </form>
                                    @if($order->canBeCancelled())
                                        <details class="mt-2">
                                            <summary class="cursor-pointer text-xs font-bold text-rose-800 hover:underline">Batalkan pesanan</summary>
                                            <form action="{{ route('employee.orders.cancel', $order) }}" method="POST" class="mt-2 min-w-56 space-y-2 rounded-lg border border-rose-200 bg-rose-50 p-3">
                                                @csrf
                                                @method('PATCH')
                                                <label class="block text-xs font-semibold text-rose-900">Alasan pembatalan
                                                    <textarea name="reason" required maxlength="500" rows="3" class="mt-1 w-full rounded-md border-rose-200 text-xs"></textarea>
                                                </label>
                                                @if($order->payment_status !== 'belum_dp')
                                                    <p class="text-[10px] leading-4 text-rose-800">Pembayaran yang tercatat tidak dihapus atau dikembalikan otomatis.</p>
                                                @endif
                                                <button class="w-full rounded-lg bg-rose-700 px-3 py-2 text-xs font-bold text-white hover:bg-rose-800" type="submit">Konfirmasi pembatalan</button>
                                            </form>
                                        </details>
                                    @endif
                                @else
                                    <span class="text-xs text-stone-500">Pesanan tidak bisa diubah.</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td class="px-5 py-10 text-center text-sm text-stone-500" colspan="5">Belum ada pesanan untuk diproses.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="border-t border-stone-100 px-5 py-4">{{ $orders->links() }}</div>
        @endif
    </div>
@endsection
