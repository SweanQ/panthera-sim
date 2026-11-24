@extends('layouts.storefront')

@section('title', 'Panthera — Custom apparel, your identity')

@push('styles')
<style>
    .cw-hero { position: relative; display: grid; min-height: 565px; grid-template-columns: 1fr 1fr; align-items: center; gap: 55px; padding-block: 58px 68px; }
    .cw-hero-copy { position: relative; z-index: 1; }
    .cw-hero h1 { max-width: 570px; margin: 20px 0 17px; font: 600 clamp(48px, 6vw, 75px)/1.02 var(--cw-display); letter-spacing: -.075em; }
    .cw-hero h1 em { color: #58753f; font-family: Georgia, serif; font-weight: 400; }
    .cw-hero-copy > p { max-width: 435px; margin-bottom: 25px; color: var(--cw-muted); font-size: 14px; line-height: 1.85; }
    .cw-hero-actions { display: flex; align-items: center; gap: 17px; flex-wrap: wrap; }
    .cw-hero-link { font-size: 12px; font-weight: 700; }
    .cw-hero-link span { margin-left: 4px; color: #58753f; }
    .cw-hero-art { position: relative; min-height: 420px; overflow: hidden; background: #d4d7c8; }
    .cw-hero-art img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: center 38%; }
    .cw-hero-art::after { position: absolute; inset: 48% 0 0; background: linear-gradient(transparent, rgba(13,20,14,.5)); content: ""; }
    .cw-art-sticker { position: absolute; z-index: 1; top: 20px; left: -20px; display: grid; width: 78px; height: 78px; place-items: center; border-radius: 50%; background: var(--cw-green); color: var(--cw-dark); text-align: center; transform: rotate(-10deg); }
    .cw-art-sticker span { max-width: 52px; font-size: 9px; font-weight: 800; line-height: 1.4; letter-spacing: .08em; text-transform: uppercase; }
    .cw-art-caption { position: absolute; z-index: 1; right: 18px; bottom: 17px; left: 18px; display: flex; align-items: end; justify-content: space-between; color: white; }
    .cw-art-caption strong { display: block; font: 600 18px var(--cw-display); }
    .cw-art-caption small { display: block; margin-top: 4px; color: rgba(255,255,255,.75); font-size: 10px; }
    .cw-art-caption > span { font-size: 10px; letter-spacing: .15em; }
    .cw-proof { display: grid; grid-template-columns: 1.35fr repeat(3, 1fr); align-items: center; gap: 20px; padding-block: 23px; border-top: 1px solid var(--cw-line); border-bottom: 1px solid var(--cw-line); }
    .cw-proof-intro { margin: 0; color: var(--cw-muted); font-size: 11px; line-height: 1.6; }
    .cw-proof-item { display: flex; align-items: center; gap: 10px; }
    .cw-proof-icon { width: 34px; height: 34px; display: grid; flex: 0 0 auto; place-items: center; border-radius: 50%; background: #e8eddf; color: #405d34; font-size: 14px; }
    .cw-proof-item strong { display: block; font-size: 11px; }
    .cw-proof-item small { display: block; margin-top: 3px; color: var(--cw-muted); font-size: 9px; }
    .cw-section { padding-block: 88px; }
    .cw-section-head { display: flex; align-items: end; justify-content: space-between; gap: 25px; margin-bottom: 27px; }
    .cw-section-head h2, .cw-process h2, .cw-inquiry h2 { max-width: 540px; margin: 12px 0 0; font: 600 clamp(34px, 4vw, 47px)/1.1 var(--cw-display); letter-spacing: -.065em; }
    .cw-section-head h2 em { color: #58753f; font-family: Georgia, serif; font-weight: 400; }
    .cw-section-head > p { max-width: 350px; margin: 0 0 2px; color: var(--cw-muted); font-size: 12px; line-height: 1.8; }
    .cw-filters { display: grid; grid-template-columns: minmax(190px, 1.5fr) repeat(2, minmax(145px, .7fr)) auto auto; gap: 9px; margin-bottom: 22px; }
    .cw-filter-submit { min-height: 43px; padding-inline: 16px; border: 0; border-radius: 5px; background: var(--cw-dark); color: white; font-size: 11px; font-weight: 700; cursor: pointer; }
    .cw-clear { min-height: 43px; display: inline-flex; align-items: center; color: var(--cw-muted); font-size: 11px; }
    .cw-results { display: flex; justify-content: space-between; margin-bottom: 12px; color: var(--cw-muted); font-size: 10px; }
    .cw-product-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; }
    .cw-product-card { min-width: 0; }
    .cw-product-image { position: relative; aspect-ratio: 4/4.5; overflow: hidden; background: #e6e5dc; }
    .cw-product-image img { width: 100%; height: 100%; object-fit: cover; transition: transform .4s ease; }
    .cw-product-card:hover .cw-product-image img { transform: scale(1.04); }
    .cw-product-badge { position: absolute; top: 10px; left: 10px; padding: 6px 8px; background: rgba(255,254,250,.92); color: #344a2a; font-size: 8px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; }
    .cw-product-info { padding-top: 13px; }
    .cw-product-info h3 { margin: 0 0 5px; font: 600 13px/1.45 var(--cw-display); }
    .cw-product-info p { min-height: 15px; margin: 0; color: var(--cw-muted); font-size: 10px; }
    .cw-product-meta { display: flex; align-items: center; justify-content: space-between; gap: 7px; margin-top: 11px; padding-top: 10px; border-top: 1px solid var(--cw-line); }
    .cw-product-meta span { color: var(--cw-muted); font-size: 9px; }
    .cw-product-meta strong { color: var(--cw-green-dark); font-size: 11px; white-space: nowrap; }
    .cw-product-action { display: block; margin-top: 10px; color: #344a2a; font-size: 10px; font-weight: 700; }
    .cw-product-action:hover { text-decoration: underline; }
    .cw-empty { grid-column: 1/-1; padding: 45px 20px; border: 1px dashed #c9cdbf; color: var(--cw-muted); text-align: center; font-size: 12px; }
    .cw-empty strong { display: block; margin-bottom: 5px; color: var(--cw-ink); font: 600 17px var(--cw-display); }
    .cw-pagination { display: flex; justify-content: center; margin-top: 28px; }
    .cw-pagination nav { display: flex; gap: 5px; }
    .cw-pagination span, .cw-pagination a { min-width: 34px; display: inline-grid; place-items: center; padding: 8px 10px; border: 1px solid var(--cw-line); border-radius: 4px; background: white; font-size: 11px; }
    .cw-pagination span[aria-current="page"] span { background: var(--cw-dark); color: white; }
    .cw-process { background: var(--cw-paper); }
    .cw-process-inner { display: grid; grid-template-columns: .9fr 1.1fr; gap: 75px; }
    .cw-process h2 { margin-bottom: 12px; }
    .cw-process-copy { max-width: 370px; color: var(--cw-muted); font-size: 12px; line-height: 1.8; }
    .cw-step-list { border-top: 1px solid #d3d8cc; }
    .cw-step { display: grid; grid-template-columns: 38px 1fr; gap: 14px; padding: 19px 0; border-bottom: 1px solid #d3d8cc; }
    .cw-step > span { color: #536e3c; font-size: 11px; font-weight: 700; }
    .cw-step h3 { margin: 0 0 5px; font: 600 13px var(--cw-display); }
    .cw-step p { margin: 0; color: var(--cw-muted); font-size: 11px; line-height: 1.7; }
    .cw-inquiry { display: grid; grid-template-columns: .8fr 1.2fr; gap: 55px; align-items: start; padding: 75px 0; }
    .cw-inquiry h2 { font-size: 38px; }
    .cw-inquiry-copy { margin-top: 12px; color: var(--cw-muted); font-size: 12px; line-height: 1.8; }
    .cw-inquiry-form { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; padding: 23px; border: 1px solid var(--cw-line); border-radius: 9px; background: var(--cw-card); }
    .cw-form-wide { grid-column: 1/-1; }
    .cw-inquiry-form textarea { min-height: 100px; resize: vertical; }
    .cw-inquiry-form .cw-button { justify-self: start; }
    .cw-inline-error { margin-top: 4px; color: #a3432f; font-size: 10px; }
    @media (max-width: 930px) {
        .cw-hero { gap: 30px; min-height: 490px; }
        .cw-hero-art { min-height: 360px; }
        .cw-product-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 27px 14px; }
        .cw-proof { grid-template-columns: repeat(3, 1fr); }
        .cw-proof-intro { grid-column: 1/-1; }
        .cw-filters { grid-template-columns: 1fr 1fr; }
        .cw-filter-submit, .cw-clear { justify-content: center; }
    }
    @media (max-width: 640px) {
        .cw-hero { grid-template-columns: 1fr; gap: 32px; padding-block: 48px 40px; }
        .cw-hero h1 { font-size: clamp(47px, 13vw, 64px); }
        .cw-hero-art { min-height: 360px; }
        .cw-art-sticker { left: -9px; width: 68px; height: 68px; }
        .cw-proof { grid-template-columns: 1fr; gap: 15px; }
        .cw-proof-intro { grid-column: auto; }
        .cw-section { padding-block: 62px; }
        .cw-section-head { align-items: flex-start; flex-direction: column; gap: 12px; }
        .cw-section-head h2 { font-size: 37px; }
        .cw-filters { grid-template-columns: 1fr 1fr; }
        .cw-filters .cw-field:first-child { grid-column: 1/-1; }
        .cw-product-info h3 { font-size: 11px; }
        .cw-product-meta { align-items: flex-start; flex-direction: column; }
        .cw-process-inner { grid-template-columns: 1fr; gap: 22px; }
        .cw-process h2 { font-size: 37px; }
        .cw-inquiry { grid-template-columns: 1fr; gap: 23px; padding-block: 62px; }
        .cw-inquiry h2 { font-size: 34px; }
        .cw-inquiry-form { grid-template-columns: 1fr; padding: 17px; }
        .cw-form-wide { grid-column: auto; }
    }
</style>
@endpush

@section('content')
<section class="cw-hero" aria-labelledby="hero-title">
    <div class="cw-hero-copy">
        <span class="cw-eyebrow">Custom apparel, made personal</span>
        <h1 id="hero-title">Bikin ide kamu <em>jadi nyata.</em></h1>
        <p>Kaos, jaket, dan apparel custom untuk tim, komunitas, atau brand yang punya cerita sendiri. Pilih produk, tentukan detail, lalu pantau prosesnya langsung.</p>
        <div class="cw-hero-actions">
            <a class="cw-button cw-button-dark" href="#koleksi">Jelajahi katalog <span aria-hidden="true">↘</span></a>
            <a class="cw-hero-link" href="{{ route('shop.orders.track') }}">Lacak pesanan <span aria-hidden="true">↗</span></a>
        </div>
    </div>
    <div class="cw-hero-art">
        <img src="https://images.unsplash.com/photo-1529139574466-a303027c1d8b?auto=format&amp;fit=crop&amp;w=1100&amp;q=85" alt="Inspirasi apparel custom dengan gaya kasual" fetchpriority="high">
        <div class="cw-art-sticker"><span>Your crew. Your way.</span></div>
        <div class="cw-art-caption">
            <div><strong>Wear your story.</strong><small>Dirancang bersama kamu.</small></div>
            <span>01 / CW</span>
        </div>
    </div>
</section>

<section class="cw-proof" aria-label="Keunggulan Panthera">
    <p class="cw-proof-intro">Dari ide pertama<br>sampai paket tiba.</p>
    <div class="cw-proof-item"><span class="cw-proof-icon" aria-hidden="true">✳</span><div><strong>Material pilihan</strong><small>Nyaman untuk dipakai</small></div></div>
    <div class="cw-proof-item"><span class="cw-proof-icon" aria-hidden="true">✓</span><div><strong>Estimasi transparan</strong><small>Harga terlihat di awal</small></div></div>
    <div class="cw-proof-item"><span class="cw-proof-icon" aria-hidden="true">↗</span><div><strong>Status bisa dilacak</strong><small>Update pesanan mandiri</small></div></div>
</section>

<section class="cw-section" id="koleksi" aria-labelledby="catalog-title">
    <div class="cw-section-head">
        <div><span class="cw-eyebrow">Pilih bentuknya</span><h2 id="catalog-title">Mulai dari produk.<br><em>Jadikan milikmu.</em></h2></div>
        <p>Gunakan pencarian dan filter untuk menemukan bahan atau produk yang paling cocok. Harga di katalog adalah estimasi per item.</p>
    </div>

    <form class="cw-filters" action="{{ route('home') }}" method="GET" aria-label="Filter katalog">
        <input class="cw-field" type="search" name="q" value="{{ $filters['q'] }}" placeholder="Cari produk atau material..." aria-label="Cari produk atau material">
        <select class="cw-field" name="stock_source" aria-label="Filter sumber produksi">
            <option value="">Semua produksi</option>
            <option value="mandiri" @selected($filters['stock_source'] === 'mandiri')>Produksi mandiri</option>
            <option value="jakarta" @selected($filters['stock_source'] === 'jakarta')>Mitra Jakarta</option>
        </select>
        <select class="cw-field" name="sort" aria-label="Urutkan produk">
            <option value="featured" @selected($filters['sort'] === 'featured')>Urutan nama</option>
            <option value="price_asc" @selected($filters['sort'] === 'price_asc')>Harga terendah</option>
            <option value="price_desc" @selected($filters['sort'] === 'price_desc')>Harga tertinggi</option>
        </select>
        <button class="cw-filter-submit" type="submit">Terapkan</button>
        @if($filters['q'] || $filters['stock_source'] || $filters['sort'] !== 'featured')
            <a class="cw-clear" href="{{ route('home') }}">Reset</a>
        @endif
    </form>

    <div class="cw-results">
        <span>{{ $products->total() }} produk ditemukan</span>
        <span>Harga dapat berubah sesuai spesifikasi dan jumlah.</span>
    </div>
    @php
        $productImages = [
            'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1598033129183-c4f50c736f10?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1551028719-00167b16eac5?auto=format&fit=crop&w=800&q=80',
            'https://images.unsplash.com/photo-1521369909029-2afed882baee?auto=format&fit=crop&w=800&q=80',
        ];
    @endphp
    <div class="cw-product-grid">
        @forelse($products as $product)
            <article class="cw-product-card">
                <div class="cw-product-image">
                    <img src="{{ $product->image_path ? asset('storage/' . $product->image_path) : $productImages[$loop->index % count($productImages)] }}" alt="{{ $product->name }}" loading="lazy">
                    <span class="cw-product-badge">Custom made</span>
                </div>
                <div class="cw-product-info">
                    <h3>{{ $product->name }}</h3>
                    <p>{{ $product->material ?: 'Material dapat disesuaikan' }}</p>
                    <div class="cw-product-meta">
                        <span>{{ $product->stock_source === 'jakarta' ? 'Mitra Jakarta' : 'Produksi mandiri' }}</span>
                        <strong>Rp {{ number_format((float) $product->base_price, 0, ',', '.') }} / item</strong>
                    </div>
                    <a class="cw-product-action" href="{{ route('shop.orders.create', ['product_id' => $product->id]) }}">Pilih produk ini <span aria-hidden="true">→</span></a>
                </div>
            </article>
        @empty
            <div class="cw-empty"><strong>Produk belum ditemukan</strong>Coba kata kunci atau filter yang berbeda.</div>
        @endforelse
    </div>
    @if($products->hasPages())
        <div class="cw-pagination">{{ $products->links() }}</div>
    @endif
</section>

<section class="cw-process" id="cara-kerja">
    <div class="cw-process-inner">
        <div>
            <span class="cw-eyebrow">Pesan tanpa ribet</span>
            <h2>Jelas dari awal.<br>Terpantau sampai akhir.</h2>
            <p class="cw-process-copy">Kirim kebutuhanmu melalui form. Pesanan akan masuk ke tim Panthera untuk dikonfirmasi sebelum produksi dimulai.</p>
            <a class="cw-button cw-button-dark" href="{{ route('shop.orders.create') }}">Mulai pesan <span aria-hidden="true">↗</span></a>
        </div>
        <div class="cw-step-list">
            <article class="cw-step"><span>01</span><div><h3>Pilih produk & detail</h3><p>Tentukan ukuran, warna, dan jumlah. Estimasi harga dihitung otomatis.</p></div></article>
            <article class="cw-step"><span>02</span><div><h3>Kirim permintaan</h3><p>Pesanan tercatat sebagai konsultasi; belum ada pembayaran sebelum tim mengonfirmasi.</p></div></article>
            <article class="cw-step"><span>03</span><div><h3>Lacak progres</h3><p>Gunakan kode pesanan dan nomor WhatsApp untuk melihat status terbaru.</p></div></article>
        </div>
    </div>
</section>

<section class="cw-inquiry" id="inquiry" aria-labelledby="inquiry-title">
    <div>
        <span class="cw-eyebrow">Butuh desain khusus?</span>
        <h2 id="inquiry-title">Ceritakan kebutuhanmu.</h2>
        <p class="cw-inquiry-copy">Untuk ide yang belum ada di katalog—misalnya kebutuhan bahan, desain, atau jumlah besar—kirim permintaan konsultasi. Tim kami akan menghubungimu melalui WhatsApp.</p>
    </div>
    <form class="cw-inquiry-form" method="POST" action="{{ route('shop.inquiries.store') }}">
        @csrf
        <div>
            <label class="cw-label" for="inquiry-name">Nama / komunitas</label>
            <input class="cw-field" id="inquiry-name" name="name" value="{{ old('name') }}" required maxlength="100" autocomplete="name">
            @error('name')<div class="cw-inline-error">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="cw-label" for="inquiry-phone">Nomor WhatsApp</label>
            <input class="cw-field" id="inquiry-phone" name="whatsapp_number" value="{{ old('whatsapp_number') }}" required maxlength="30" placeholder="08xx atau 62xx" autocomplete="tel">
            @error('whatsapp_number')<div class="cw-inline-error">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="cw-label" for="inquiry-product">Produk yang diminati (opsional)</label>
            <select class="cw-field" id="inquiry-product" name="product_id">
                <option value="">Belum tahu / lainnya</option>
                @foreach($inquiryProducts as $product)
                    <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="cw-label" for="inquiry-quantity">Perkiraan jumlah (opsional)</label>
            <input class="cw-field" id="inquiry-quantity" type="number" name="quantity" value="{{ old('quantity') }}" min="1" max="5000" placeholder="Contoh: 30">
            @error('quantity')<div class="cw-inline-error">{{ $message }}</div>@enderror
        </div>
        <div class="cw-form-wide">
            <label class="cw-label" for="inquiry-details">Ceritakan kebutuhanmu</label>
            <textarea class="cw-field" id="inquiry-details" name="details" required minlength="15" maxlength="2000" placeholder="Jenis apparel, ide desain, material, tenggat, atau detail lain...">{{ old('details') }}</textarea>
            @error('details')<div class="cw-inline-error">{{ $message }}</div>@enderror
        </div>
        <div class="cw-form-wide"><button class="cw-button cw-button-dark" type="submit">Kirim permintaan <span aria-hidden="true">→</span></button></div>
    </form>
</section>
@endsection
