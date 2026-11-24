@extends('layouts.panthera')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-zinc-900">Laporan Transaksi & Keuangan</h1>
            <p class="text-sm text-stone-600 mt-1">Rekapitulasi pembayaran DP dan pelunasan Panthera.</p>
        </div>
        <a href="{{ route('orders.index') }}" class="text-sm text-stone-600 hover:text-zinc-900">&larr; Kembali ke Dashboard</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="p-6 rounded-xl bg-zinc-900/70 border border-zinc-800/90">
            <div class="text-xs uppercase tracking-wider text-white">Total Pendapatan Diterima</div>
            <div class="text-3xl font-semibold text-white mt-2">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
        </div>
        <div class="p-6 rounded-xl bg-zinc-900/70 border border-zinc-800/90">
            <div class="text-xs uppercase tracking-wider text-white">Sisa Piutang Pelunasan</div>
            <div class="text-3xl font-semibold text-white mt-2">Rp {{ number_format($totalReceivable, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="rounded-xl bg-zinc-900/70 border border-zinc-800/90 overflow-hidden">
        <div class="p-5 border-b border-zinc-800/90">
            <h2 class="text-base font-semibold text-white">Riwayat Transaksi Masuk</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-800/90 text-sm">
                <thead class="bg-zinc-950/60 text-white text-xs uppercase">
                    <tr>
                        <th class="px-5 py-3 text-left">Tanggal</th>
                        <th class="px-5 py-3 text-left">Kode Pesanan</th>
                        <th class="px-5 py-3 text-left">Pelanggan</th>
                        <th class="px-5 py-3 text-left">Jenis</th>
                        <th class="px-5 py-3 text-left">Metode</th>
                        <th class="px-5 py-3 text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/70">
                    @foreach($payments as $pay)
                    <tr>
                        <td class="px-5 py-3.5 text-white">{{ $pay->payment_date }}</td>
                        <td class="px-5 py-3.5 font-mono text-white">{{ $pay->order->order_code }}</td>
                        <td class="px-5 py-3.5 text-white">{{ $pay->order->customer->name }}</td>
                        <td class="px-5 py-3.5 uppercase text-xs text-white">{{ $pay->payment_type }}</td>
                        <td class="px-5 py-3.5 uppercase text-xs text-white">{{ $pay->payment_method }}</td>
                        <td class="px-5 py-3.5 text-right font-semibold text-white">Rp {{ number_format($pay->amount, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection