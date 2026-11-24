@extends('layouts.storefront')

@section('title', 'Lacak pesanan — Panthera')

@push('styles')
<style>
    .cw-track-wrap { width: min(620px, 100%); margin: 66px auto; }
    .cw-track-card { padding: clamp(23px, 6vw, 42px); border: 1px solid var(--cw-line); border-radius: 10px; background: var(--cw-card); }
    .cw-track-card h1 { margin: 13px 0 8px; font: 600 clamp(34px, 5vw, 46px)/1.1 var(--cw-display); letter-spacing: -.06em; }
    .cw-track-card > p { margin-bottom: 22px; color: var(--cw-muted); font-size: 12px; line-height: 1.8; }
    .cw-track-form { display: grid; gap: 15px; }
</style>
@endpush

@section('content')
<section class="cw-track-wrap">
    <div class="cw-track-card">
        <span class="cw-eyebrow">Update pesanan</span>
        <h1>Lacak pesananmu.</h1>
        <p>Masukkan kode pesanan dan nomor WhatsApp yang digunakan saat memesan. Informasi pesanan hanya ditampilkan jika keduanya cocok.</p>
        @if($errors->any())
            <div class="cw-error" role="alert" style="margin-bottom:16px">{{ $errors->first() }}</div>
        @endif
        <form class="cw-track-form" method="POST" action="{{ route('shop.orders.track.submit') }}">
            @csrf
            <div>
                <label class="cw-label" for="track-code">Kode pesanan</label>
                <input class="cw-field" id="track-code" name="order_code" value="{{ old('order_code') }}" required maxlength="40" placeholder="Contoh: PNT-01J...">
            </div>
            <div>
                <label class="cw-label" for="track-phone">Nomor WhatsApp</label>
                <input class="cw-field" id="track-phone" name="whatsapp_number" value="{{ old('whatsapp_number') }}" required maxlength="30" placeholder="Nomor saat memesan" autocomplete="tel">
            </div>
            <button class="cw-button cw-button-dark" type="submit">Lihat status <span aria-hidden="true">→</span></button>
        </form>
    </div>
</section>
@endsection
