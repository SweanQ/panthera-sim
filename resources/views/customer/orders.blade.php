@extends('layouts.storefront')

@section('title', 'Riwayat pesanan — Panthera')

@push('styles')
<style>
    .cw-history-head { padding: 46px 0 23px; }
    .cw-history-head h1 { margin: 12px 0 7px; font: 600 clamp(34px, 5vw, 46px)/1.08 var(--cw-display); letter-spacing: -.06em; }
    .cw-history-head p { margin: 0; color: var(--cw-muted); font-size: 12px; line-height: 1.8; }
    .cw-history-list { display: grid; gap: 12px; padding-bottom: 65px; }
    .cw-history-card { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 18px; padding: 20px; border: 1px solid var(--cw-line); border-radius: 8px; background: var(--cw-card); }
    .cw-history-code { color: #536e3c; font: 700 11px var(--cw-font); letter-spacing: .04em; }
    .cw-history-card h2 { margin: 7px 0 5px; font: 600 15px var(--cw-display); }
    .cw-history-specs { color: var(--cw-muted); font-size: 10px; line-height: 1.7; }
    .cw-history-total { margin-top: 11px; font-size: 11px; font-weight: 700; }
    .cw-history-state { display: flex; flex-direction: column; align-items: flex-end; justify-content: space-between; gap: 13px; }
    .cw-history-state span { padding: 7px 10px; border-radius: 30px; background: #e7efdc; color: #405b32; font-size: 9px; font-weight: 800; text-transform: uppercase; }
    .cw-history-state small { color: var(--cw-muted); font-size: 9px; }
    .cw-history-empty { padding: 42px 20px; border: 1px dashed #c9cdbf; border-radius: 8px; color: var(--cw-muted); text-align: center; font-size: 12px; }
    .cw-history-empty strong { display: block; margin-bottom: 8px; color: var(--cw-ink); font: 600 18px var(--cw-display); }
    .cw-history-empty .cw-button { margin-top: 16px; }
    @media (max-width: 560px) {
        .cw-history-card { grid-template-columns: 1fr; }
        .cw-history-state { align-items: flex-start; flex-direction: row; }
    }
</style>
@endpush

@section('content')
<header class="cw-history-head">
    <span class="cw-eyebrow">Akun Panthera</span>
    <h1>Riwayat pesanan</h1>
    <p>Hai, {{ auth()->user()->name }}. Berikut pesanan yang terhubung ke akunmu.</p>
</header>

<section class="cw-history-list" aria-label="Daftar pesanan">
    @forelse($orders as $order)
        @php
            $statuses = [
                'konsultasi' => 'Menunggu konfirmasi',
                'desain_disetujui' => 'Desain disetujui',
                'proses_sablon' => 'Dalam produksi',
                'selesai' => 'Selesai',
                'diambil' => 'Diterima',
                'dibatalkan' => 'Dibatalkan',
            ];
        @endphp
        <article class="cw-history-card">
            <div>
                <div class="cw-history-code">{{ $order->order_code }}</div>
                <h2>{{ $order->product->name }}</h2>
                <div class="cw-history-specs">{{ $order->quantity }} pcs · Ukuran {{ $order->size }} · {{ $order->color }}<br>Pesanan {{ $order->created_at->format('d M Y') }}</div>
                <div class="cw-history-total">Estimasi Rp {{ number_format((float) $order->total_price, 0, ',', '.') }}</div>
                @if($order->production_status === 'dibatalkan')
                    <p class="cw-help">Pesanan ini dibatalkan. Pembayaran yang tercatat tidak otomatis dikembalikan.</p>
                @endif
            </div>
            <div class="cw-history-state">
                <span>{{ $statuses[$order->production_status] ?? $order->production_status }}</span>
                <small>Pembayaran: {{ strtoupper(str_replace('_', ' ', $order->payment_status)) }}</small>
            </div>
        </article>
    @empty
        <div class="cw-history-empty">
            <strong>Belum ada pesanan di akunmu</strong>
            Pesanan yang dibuat saat masuk akan otomatis muncul di sini.
            <div><a class="cw-button cw-button-dark" href="{{ route('home') }}#koleksi">Jelajahi katalog</a></div>
        </div>
    @endforelse
    @if($orders->hasPages())
        <div class="cw-pagination">{{ $orders->links() }}</div>
    @endif
</section>
@endsection
