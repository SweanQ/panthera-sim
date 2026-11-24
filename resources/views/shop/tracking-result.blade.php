@extends('layouts.storefront')

@section('title', 'Status pesanan — Panthera')

@push('styles')
<style>
    .cw-track-wrap { width: min(700px, 100%); margin: 55px auto; }
    .cw-track-card { padding: clamp(22px, 5vw, 38px); border: 1px solid var(--cw-line); border-radius: 10px; background: var(--cw-card); }
    .cw-track-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 15px; }
    .cw-track-head h1 { margin: 10px 0 4px; font: 600 clamp(28px, 4vw, 38px)/1.1 var(--cw-display); letter-spacing: -.05em; }
    .cw-track-code { color: var(--cw-muted); font: 600 11px var(--cw-font); }
    .cw-status-pill { padding: 8px 10px; border-radius: 50px; background: #e6efd9; color: #39572c; font-size: 9px; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; }
    .cw-status-pill.is-cancelled { background: #fce8e6; color: #8d2922; }
    .cw-cancelled-message { margin: 20px 0; padding: 14px; border: 1px solid #efc3bd; border-radius: 7px; background: #fff1ef; color: #7d332c; font-size: 11px; line-height: 1.7; }
    .cw-track-item { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin: 22px 0; padding: 16px 0; border-top: 1px solid var(--cw-line); border-bottom: 1px solid var(--cw-line); }
    .cw-track-item span { display: block; color: var(--cw-muted); font-size: 9px; }
    .cw-track-item strong { display: block; margin-top: 5px; font-size: 11px; }
    .cw-timeline { display: grid; gap: 0; margin: 20px 0; }
    .cw-timeline-step { position: relative; display: grid; grid-template-columns: 22px 1fr; gap: 12px; min-height: 50px; }
    .cw-timeline-step:not(:last-child)::before { position: absolute; top: 20px; bottom: -3px; left: 8px; width: 1px; background: #dfe3d8; content: ""; }
    .cw-timeline-dot { z-index: 1; width: 17px; height: 17px; display: grid; place-items: center; border: 1px solid #d5dacd; border-radius: 50%; background: var(--cw-card); color: transparent; font-size: 9px; }
    .cw-timeline-step.is-done .cw-timeline-dot { border-color: #668d4d; background: #668d4d; color: white; }
    .cw-timeline-step strong { display: block; font-size: 11px; }
    .cw-timeline-step small { display: block; margin-top: 3px; color: var(--cw-muted); font-size: 9px; }
    .cw-track-bottom { display: flex; align-items: center; justify-content: space-between; gap: 15px; padding-top: 13px; border-top: 1px solid var(--cw-line); font-size: 10px; }
    .cw-track-bottom span { color: var(--cw-muted); }
    .cw-track-actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 22px; }
    @media (max-width: 480px) { .cw-track-head { flex-direction: column; } .cw-track-item { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
@php
    $steps = [
        'konsultasi' => 'Permintaan diterima',
        'desain_disetujui' => 'Desain disetujui',
        'proses_sablon' => 'Dalam produksi',
        'selesai' => 'Pesanan selesai',
        'diambil' => 'Pesanan diterima',
    ];
    $isCancelled = $order->production_status === 'dibatalkan';
    $currentStep = $isCancelled ? -1 : array_search($order->production_status, array_keys($steps), true);
@endphp
<section class="cw-track-wrap">
    <div class="cw-track-card">
        <div class="cw-track-head">
            <div>
                <span class="cw-eyebrow">Status pesanan</span>
                <h1>{{ $isCancelled ? 'Pesanan dibatalkan' : ($steps[$order->production_status] ?? 'Status diperbarui') }}</h1>
                <div class="cw-track-code">{{ $order->order_code }}</div>
            </div>
            <span class="cw-status-pill {{ $isCancelled ? 'is-cancelled' : '' }}">{{ str_replace('_', ' ', $order->production_status) }}</span>
        </div>
        <div class="cw-track-item">
            <div><span>Produk</span><strong>{{ $order->product->name }}</strong></div>
            <div><span>Jumlah & spesifikasi</span><strong>{{ $order->quantity }} pcs · {{ $order->size }} · {{ $order->color }}</strong></div>
            <div><span>Estimasi total</span><strong>Rp {{ number_format((float) $order->total_price, 0, ',', '.') }}</strong></div>
            <div><span>Terakhir diperbarui</span><strong>{{ $order->updated_at->format('d M Y, H:i') }}</strong></div>
        </div>
        @if($isCancelled)
            <div class="cw-cancelled-message">Pesanan ini dibatalkan sebelum produksi dimulai. Pembayaran yang tercatat tidak otomatis dikembalikan; silakan hubungi tim Panthera untuk informasi lebih lanjut.</div>
        @else
            <div class="cw-timeline" aria-label="Tahapan produksi">
                @foreach($steps as $key => $label)
                    @php($stepIndex = array_search($key, array_keys($steps), true))
                    <div class="cw-timeline-step {{ $stepIndex <= $currentStep ? 'is-done' : '' }}">
                        <span class="cw-timeline-dot" aria-hidden="true">✓</span>
                        <div><strong>{{ $label }}</strong>@if($key === $order->production_status)<small>Status saat ini</small>@endif</div>
                    </div>
                @endforeach
            </div>
        @endif
        <div class="cw-track-bottom"><span>Status pembayaran</span><strong>{{ strtoupper(str_replace('_', ' ', $order->payment_status)) }}</strong></div>
        @if($order->notes)
            <p class="cw-help">Catatan: {{ $order->notes }}</p>
        @endif
        <div class="cw-track-actions">
            <a class="cw-button cw-button-dark" href="{{ route('shop.orders.track') }}">Lacak pesanan lain</a>
            <a class="cw-button cw-button-outline" href="{{ route('home') }}">Kembali ke Panthera</a>
        </div>
    </div>
</section>
@endsection
