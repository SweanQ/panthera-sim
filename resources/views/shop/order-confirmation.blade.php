@extends('layouts.storefront')

@section('title', 'Pesanan diterima — Panthera')

@push('styles')
<style>
    .cw-confirm-wrap { max-width: 730px; margin: 62px auto; }
    .cw-confirm-card { padding: clamp(24px, 6vw, 46px); border: 1px solid var(--cw-line); border-radius: 12px; background: var(--cw-card); }
    .cw-confirm-icon { width: 48px; height: 48px; display: grid; place-items: center; border-radius: 50%; background: #e4f1d2; color: #35572b; font-size: 22px; }
    .cw-confirm-card h1 { margin: 20px 0 10px; font: 600 clamp(33px, 5vw, 46px)/1.1 var(--cw-display); letter-spacing: -.06em; }
    .cw-confirm-card > p { color: var(--cw-muted); font-size: 12px; line-height: 1.8; }
    .cw-code { margin: 22px 0; padding: 17px; border: 1px dashed #b6c09f; border-radius: 7px; background: #f1f5e9; }
    .cw-code small { display: block; color: var(--cw-muted); font-size: 10px; }
    .cw-code strong { display: block; margin-top: 6px; color: var(--cw-green-dark); font: 700 21px var(--cw-display); letter-spacing: .04em; }
    .cw-confirm-items { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; padding-block: 16px; border-top: 1px solid var(--cw-line); border-bottom: 1px solid var(--cw-line); }
    .cw-confirm-items span { display: block; color: var(--cw-muted); font-size: 10px; }
    .cw-confirm-items strong { display: block; margin-top: 4px; font-size: 12px; }
    .cw-confirm-actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 22px; }
    @media (max-width: 520px) { .cw-confirm-wrap { margin-block: 35px; } .cw-confirm-items { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<section class="cw-confirm-wrap">
    <div class="cw-confirm-card">
        <div class="cw-confirm-icon" aria-hidden="true">✓</div>
        <span class="cw-eyebrow" style="margin-top:19px">Permintaan diterima</span>
        <h1>Terima kasih, {{ $order->customer->name }}.</h1>
        <p>Permintaan pesananmu sudah tercatat dan menunggu konfirmasi tim Panthera. Belum ada pembayaran yang diproses.</p>
        <div class="cw-code">
            <small>Simpan kode pesanan untuk pelacakan</small>
            <strong>{{ $order->order_code }}</strong>
        </div>
        <div class="cw-confirm-items">
            <div><span>Produk</span><strong>{{ $order->product->name }}</strong></div>
            <div><span>Jumlah</span><strong>{{ $order->quantity }} pcs</strong></div>
            <div><span>Ukuran & warna</span><strong>{{ $order->size }} · {{ $order->color }}</strong></div>
            <div><span>Estimasi total</span><strong>Rp {{ number_format((float) $order->total_price, 0, ',', '.') }}</strong></div>
        </div>
        <div class="cw-confirm-actions">
            <a class="cw-button cw-button-dark" href="{{ route('shop.orders.track') }}">Lacak pesanan <span aria-hidden="true">→</span></a>
            @if(auth()->user()?->role === 'customer')
                <a class="cw-button cw-button-outline" href="{{ route('customer.orders') }}">Riwayat pesanan</a>
            @endif
            <a class="cw-button cw-button-outline" href="{{ route('home') }}">Kembali ke katalog</a>
        </div>
        <p class="cw-help">Saat melacak, gunakan kode di atas dan nomor WhatsApp yang dipakai saat memesan.</p>
    </div>
</section>
@endsection
