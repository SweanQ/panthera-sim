<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f5f4ef">
    <meta name="description" content="Panthera membantu tim dan brand membuat apparel custom dengan material pilihan dan produksi yang rapi.">
    <title>Panthera — Apparel custom, cerita Anda</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: light;
            --ink: #20251f;
            --muted: #73796f;
            --paper: #f5f4ef;
            --white: #fffefa;
            --line: #e8e7df;
            --green: #355b42;
            --green-dark: #233d2c;
            --lime: #d7ed9c;
            --serif: Georgia, "Times New Roman", serif;
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            background: var(--paper);
            color: var(--ink);
            font-family: "DM Sans", sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        a { color: inherit; text-decoration: none; }
        button, a { -webkit-tap-highlight-color: transparent; }
        .shell { width: min(1180px, calc(100% - 64px)); margin: 0 auto; }
        .topbar {
            height: 78px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(32, 37, 31, .09);
        }
        .brand { display: inline-flex; align-items: center; gap: 11px; }
        .brand-mark {
            display: grid;
            width: 36px;
            height: 36px;
            place-items: center;
            border-radius: 50%;
            background: var(--green);
            color: white;
            font: 800 17px "Manrope", sans-serif;
            letter-spacing: -2px;
        }
        .brand-name {
            font: 800 15px "Manrope", sans-serif;
            letter-spacing: .13em;
        }
        .brand-name span { color: var(--green); }
        .nav-links { display: flex; align-items: center; gap: 35px; }
        .nav-links a { color: #62685f; font-size: 13px; transition: color .2s ease; }
        .nav-links a:hover { color: var(--green); }
        .nav-login {
            padding: 11px 17px;
            border: 1px solid #d5d8ce;
            border-radius: 5px;
            color: var(--ink) !important;
            font-weight: 600;
        }
        .nav-login:hover { border-color: var(--green); }

        .hero {
            position: relative;
            display: grid;
            min-height: 570px;
            grid-template-columns: 1.03fr .97fr;
            align-items: center;
            gap: 48px;
            padding: 62px 0 70px;
        }
        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            color: var(--green);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .16em;
            text-transform: uppercase;
        }
        .eyebrow::before { width: 24px; height: 1px; background: currentColor; content: ""; }
        h1, h2, h3, p { margin-top: 0; }
        .hero h1 {
            max-width: 600px;
            margin: 22px 0 20px;
            font: 600 clamp(48px, 6vw, 76px)/1.04 "Manrope", sans-serif;
            letter-spacing: -.075em;
        }
        .hero h1 em { color: var(--green); font-family: var(--serif); font-weight: 400; }
        .hero-copy {
            max-width: 425px;
            margin-bottom: 28px;
            color: var(--muted);
            font-size: 15px;
            line-height: 1.85;
        }
        .hero-actions { display: flex; align-items: center; gap: 19px; }
        .button {
            display: inline-flex;
            min-height: 48px;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 0 19px;
            border: 1px solid var(--green);
            border-radius: 4px;
            background: var(--green);
            color: white;
            font-size: 13px;
            font-weight: 700;
            transition: background .2s ease, transform .2s ease;
        }
        .button:hover { transform: translateY(-2px); background: var(--green-dark); }
        .button svg { width: 15px; height: 15px; }
        .text-link { color: var(--ink); font-size: 13px; font-weight: 700; }
        .text-link span { padding-left: 5px; color: var(--green); }
        .hero-visual {
            position: relative;
            min-height: 425px;
            overflow: hidden;
            background: #d5d4c8;
        }
        .hero-photo {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center 38%;
        }
        .hero-visual::after {
            position: absolute;
            inset: 45% 0 0;
            background: linear-gradient(transparent, rgba(20, 32, 23, .42));
            content: "";
        }
        .image-note {
            position: absolute;
            z-index: 1;
            right: 20px;
            bottom: 20px;
            left: 20px;
            display: flex;
            align-items: end;
            justify-content: space-between;
            color: white;
        }
        .image-note strong { display: block; font: 600 18px "Manrope", sans-serif; }
        .image-note span { display: block; margin-top: 5px; color: rgba(255,255,255,.77); font-size: 11px; }
        .image-index { font: 500 12px "DM Sans", sans-serif; letter-spacing: .12em; }
        .hero-stamp {
            position: absolute;
            z-index: 2;
            top: 38px;
            left: -26px;
            display: grid;
            width: 83px;
            height: 83px;
            place-items: center;
            border: 1px solid rgba(32, 37, 31, .1);
            border-radius: 50%;
            background: var(--lime);
            color: var(--green-dark);
            text-align: center;
            transform: rotate(-12deg);
        }
        .hero-stamp span { max-width: 56px; font-size: 9px; font-weight: 700; line-height: 1.4; letter-spacing: .08em; text-transform: uppercase; }

        .proof {
            display: grid;
            grid-template-columns: 1.4fr repeat(3, 1fr);
            align-items: center;
            gap: 20px;
            padding: 25px 0;
            border-top: 1px solid var(--line);
            border-bottom: 1px solid var(--line);
        }
        .proof-intro { color: var(--muted); font-size: 12px; line-height: 1.6; }
        .proof-item { display: flex; align-items: center; gap: 11px; }
        .proof-icon {
            display: grid;
            width: 34px;
            height: 34px;
            flex: 0 0 auto;
            place-items: center;
            border-radius: 50%;
            background: #e8ebdf;
            color: var(--green);
        }
        .proof-icon svg { width: 16px; height: 16px; }
        .proof-item strong { display: block; font-size: 12px; }
        .proof-item span { display: block; margin-top: 3px; color: var(--muted); font-size: 10px; }

        .section { padding: 98px 0; }
        .section-heading {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 28px;
            margin-bottom: 34px;
        }
        .section-heading h2 {
            max-width: 510px;
            margin: 13px 0 0;
            font: 600 clamp(34px, 4vw, 48px)/1.12 "Manrope", sans-serif;
            letter-spacing: -.06em;
        }
        .section-heading h2 em { color: var(--green); font-family: var(--serif); font-weight: 400; }
        .section-heading p { max-width: 325px; margin: 0 0 3px; color: var(--muted); font-size: 13px; line-height: 1.8; }
        .catalog-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 17px; }
        .product-card { min-width: 0; }
        .product-image {
            position: relative;
            aspect-ratio: 4 / 4.65;
            overflow: hidden;
            background: #e8e6dc;
        }
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .45s cubic-bezier(.2,.7,.2,1);
        }
        .product-card:hover .product-image img { transform: scale(1.045); }
        .product-tag {
            position: absolute;
            top: 12px;
            left: 12px;
            padding: 7px 9px;
            background: rgba(255, 254, 250, .92);
            color: var(--green-dark);
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .product-details { padding: 15px 1px 0; }
        .product-details h3 { margin: 0 0 7px; font: 600 14px/1.45 "Manrope", sans-serif; }
        .product-material { min-height: 16px; color: var(--muted); font-size: 11px; }
        .product-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px solid var(--line);
        }
        .product-bottom span { color: var(--muted); font-size: 10px; }
        .product-bottom strong { color: var(--green-dark); font-size: 13px; }
        .empty-state {
            grid-column: 1 / -1;
            padding: 50px 20px;
            border: 1px dashed #c9cbbf;
            color: var(--muted);
            text-align: center;
            font-size: 14px;
        }
        .empty-state strong { display: block; margin-bottom: 7px; color: var(--ink); font: 600 18px "Manrope", sans-serif; }

        .process { background: #e9ebe2; }
        .process-inner { display: grid; grid-template-columns: .9fr 1.1fr; gap: 90px; align-items: start; }
        .process-title h2 {
            max-width: 410px;
            margin: 15px 0;
            font: 600 clamp(34px, 4vw, 49px)/1.1 "Manrope", sans-serif;
            letter-spacing: -.065em;
        }
        .process-title p { max-width: 355px; color: var(--muted); font-size: 13px; line-height: 1.8; }
        .steps { border-top: 1px solid #d5d8ce; }
        .step {
            display: grid;
            grid-template-columns: 42px 1fr;
            gap: 18px;
            padding: 21px 0;
            border-bottom: 1px solid #d5d8ce;
        }
        .step-number { color: var(--green); font: 500 12px "DM Sans", sans-serif; }
        .step h3 { margin: 0 0 5px; font: 600 15px "Manrope", sans-serif; }
        .step p { margin: 0; color: var(--muted); font-size: 12px; line-height: 1.7; }

        .closing { padding: 87px 0; }
        .closing-card {
            position: relative;
            overflow: hidden;
            padding: 55px 58px;
            background: var(--green-dark);
            color: white;
        }
        .closing-card::after {
            position: absolute;
            top: -110px;
            right: -40px;
            width: 320px;
            height: 320px;
            border: 1px solid rgba(255,255,255,.18);
            border-radius: 50%;
            box-shadow: 0 0 0 28px rgba(255,255,255,.035), 0 0 0 58px rgba(255,255,255,.03);
            content: "";
        }
        .closing-content { position: relative; z-index: 1; max-width: 600px; }
        .closing .eyebrow { color: var(--lime); }
        .closing h2 {
            margin: 14px 0 13px;
            font: 600 clamp(33px, 4vw, 48px)/1.1 "Manrope", sans-serif;
            letter-spacing: -.06em;
        }
        .closing p { max-width: 445px; margin-bottom: 23px; color: rgba(255,255,255,.68); font-size: 13px; line-height: 1.8; }
        .closing .button { border-color: var(--lime); background: var(--lime); color: var(--green-dark); }
        .closing .button:hover { background: #e4f6b7; }
        footer { padding: 23px 0 29px; border-top: 1px solid var(--line); }
        .footer-inner { display: flex; align-items: center; justify-content: space-between; gap: 20px; }
        .footer-copy { color: var(--muted); font-size: 11px; }
        .footer-links { display: flex; gap: 22px; color: var(--muted); font-size: 11px; }
        .footer-links a:hover { color: var(--green); }

        @media (max-width: 900px) {
            .hero { min-height: 490px; gap: 28px; }
            .hero-visual { min-height: 370px; }
            .catalog-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); row-gap: 30px; }
            .process-inner { gap: 45px; }
            .proof { grid-template-columns: repeat(3, 1fr); }
            .proof-intro { grid-column: 1 / -1; }
        }
        @media (max-width: 640px) {
            .shell { width: min(100% - 38px, 480px); }
            .topbar { height: 67px; }
            .nav-links { gap: 14px; }
            .nav-links > a:not(.nav-login) { display: none; }
            .nav-login { padding: 9px 12px; font-size: 11px !important; }
            .hero { grid-template-columns: 1fr; gap: 35px; padding: 53px 0 44px; }
            .hero h1 { max-width: 470px; font-size: clamp(47px, 14vw, 68px); }
            .hero-copy { font-size: 14px; }
            .hero-visual { min-height: 390px; }
            .hero-stamp { top: 18px; left: -12px; width: 70px; height: 70px; }
            .proof { grid-template-columns: 1fr; gap: 16px; padding: 21px 0; }
            .proof-intro { grid-column: auto; }
            .proof-item { gap: 10px; }
            .section { padding: 70px 0; }
            .section-heading { align-items: start; flex-direction: column; gap: 13px; margin-bottom: 25px; }
            .section-heading h2 { font-size: 38px; }
            .catalog-grid { gap: 27px 12px; }
            .product-details h3 { font-size: 12px; }
            .product-bottom { align-items: start; flex-direction: column; gap: 5px; }
            .product-bottom strong { font-size: 12px; }
            .product-tag { top: 8px; left: 8px; padding: 6px 7px; font-size: 8px; }
            .process-inner { grid-template-columns: 1fr; gap: 24px; }
            .process-title h2 { font-size: 39px; }
            .closing { padding: 62px 0; }
            .closing-card { padding: 38px 25px; }
            .closing-card::after { top: -140px; right: -200px; }
            .footer-inner { align-items: start; flex-direction: column; }
            .footer-links { gap: 17px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; }
        }
    </style>
</head>
<body>
    <header class="shell">
        <nav class="topbar" aria-label="Navigasi utama">
            <a class="brand" href="{{ route('home') }}" aria-label="Panthera, beranda">
                <span class="brand-mark">P.</span>
                <span class="brand-name">PANTHERA<span>®</span></span>
            </a>
            <div class="nav-links">
                <a href="#koleksi">Koleksi</a>
                <a href="#proses">Cara kerja</a>
                <a class="nav-login" href="{{ route('login') }}">Masuk portal <span aria-hidden="true">↗</span></a>
            </div>
        </nav>
    </header>

    <main>
        <section class="shell hero" aria-labelledby="hero-title">
            <div class="hero-copy-wrap">
                <span class="eyebrow">Custom apparel studio</span>
                <h1 id="hero-title">Bukan sekadar seragam. <em>Identitas.</em></h1>
                <p class="hero-copy">Dari kaos komunitas sampai jaket tim, Panthera membantu mengubah ide Anda menjadi apparel yang nyaman dipakai dan enak dilihat.</p>
                <div class="hero-actions">
                    <a class="button" href="#koleksi">
                        Lihat koleksi
                        <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M8 3l5 5-5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                    <a class="text-link" href="#proses">Kenali Panthera <span aria-hidden="true">↘</span></a>
                </div>
            </div>
            <div class="hero-visual">
                <img class="hero-photo" src="https://images.unsplash.com/photo-1529139574466-a303027c1d8b?auto=format&amp;fit=crop&amp;w=1100&amp;q=85" alt="Koleksi apparel dengan gaya kasual modern" fetchpriority="high">
                <div class="hero-stamp"><span>Dibuat dengan detail</span></div>
                <div class="image-note">
                    <div><strong>Wear your story.</strong><span>Setiap detail punya arti.</span></div>
                    <span class="image-index">P / 01</span>
                </div>
            </div>
        </section>

        <section class="shell proof" aria-label="Keunggulan Panthera">
            <p class="proof-intro">Kualitas yang terasa.<br>Proses yang terarah.</p>
            <div class="proof-item">
                <span class="proof-icon" aria-hidden="true">
                    <svg viewBox="0 0 20 20" fill="none"><path d="M5 4h10v12H5zM8 7h4M8 10h4M8 13h2" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <div><strong>Material pilihan</strong><span>Nyaman untuk harian</span></div>
            </div>
            <div class="proof-item">
                <span class="proof-icon" aria-hidden="true">
                    <svg viewBox="0 0 20 20" fill="none"><path d="m3 10 4 4 10-9" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <div><strong>Detail terjaga</strong><span>Dari desain ke produksi</span></div>
            </div>
            <div class="proof-item">
                <span class="proof-icon" aria-hidden="true">
                    <svg viewBox="0 0 20 20" fill="none"><path d="M3 6h9v8H3zM12 9h3l2 2v3h-5zM6 16a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3ZM14 16a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <div><strong>Siap dikirim</strong><span>Produksi terkoordinasi</span></div>
            </div>
        </section>

        <section class="shell section" id="koleksi" aria-labelledby="catalog-title">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">Dibuat untuk jadi milik Anda</span>
                    <h2 id="catalog-title">Mulai dari produk.<br><em>Jadikan personal.</em></h2>
                </div>
                <p>Pilih produk yang paling sesuai dengan kebutuhan tim, komunitas, atau brand Anda. Setiap item dapat dibuat dengan sentuhan custom.</p>
            </div>

            @php
                $productImages = [
                    'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1598033129183-c4f50c736f10?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1551028719-00167b16eac5?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1521369909029-2afed882baee?auto=format&fit=crop&w=800&q=80',
                ];
            @endphp
            <div class="catalog-grid">
                @forelse ($products as $product)
                    <article class="product-card">
                        <div class="product-image">
                            <img src="{{ $productImages[$loop->index % count($productImages)] }}" alt="{{ $product->name }}" loading="lazy">
                            <span class="product-tag">Custom made</span>
                        </div>
                        <div class="product-details">
                            <h3>{{ $product->name }}</h3>
                            <div class="product-material">{{ $product->material ?: 'Material dapat disesuaikan' }}</div>
                            <div class="product-bottom">
                                <span>{{ $product->stock_source === 'jakarta' ? 'Mitra produksi Jakarta' : 'Produksi mandiri' }}</span>
                                <strong>Mulai Rp {{ number_format((float) $product->base_price, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="empty-state">
                        <strong>Koleksi sedang disiapkan</strong>
                        Silakan kembali lagi untuk melihat produk Panthera.
                    </div>
                @endforelse
            </div>
        </section>

        <section class="process section" id="proses" aria-labelledby="process-title">
            <div class="shell process-inner">
                <div class="process-title">
                    <span class="eyebrow">Dari ide sampai jadi</span>
                    <h2 id="process-title">Proses yang jelas. Hasil yang terasa.</h2>
                    <p>Setiap pesanan dimulai dari kebutuhan Anda, lalu dikerjakan bertahap dengan perhatian pada bahan dan detail.</p>
                </div>
                <div class="steps">
                    <article class="step">
                        <span class="step-number">01</span>
                        <div><h3>Ceritakan kebutuhan</h3><p>Tentukan jenis produk, jumlah, dan gambaran desain yang Anda inginkan.</p></div>
                    </article>
                    <article class="step">
                        <span class="step-number">02</span>
                        <div><h3>Matangkan detail</h3><p>Pilih material dan selaraskan desain sebelum pesanan masuk ke tahap produksi.</p></div>
                    </article>
                    <article class="step">
                        <span class="step-number">03</span>
                        <div><h3>Produksi dan pengiriman</h3><p>Pesanan dipantau sampai selesai dan siap dikirim ke tujuan Anda.</p></div>
                    </article>
                </div>
            </div>
        </section>

        <section class="shell closing" aria-labelledby="closing-title">
            <div class="closing-card">
                <div class="closing-content">
                    <span class="eyebrow">Langkah berikutnya</span>
                    <h2 id="closing-title">Punya ide untuk dibuat nyata?</h2>
                    <p>Jelajahi katalog untuk menemukan titik awal. Tim Panthera dapat membantu mengelola pesanan dan proses produksi melalui portal.</p>
                    <a class="button" href="{{ route('login') }}">
                        Masuk ke portal
                        <svg viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M8 3l5 5-5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="shell footer-inner">
            <a class="brand" href="{{ route('home') }}" aria-label="Panthera, kembali ke beranda">
                <span class="brand-mark">P.</span>
                <span class="brand-name">PANTHERA<span>®</span></span>
            </a>
            <span class="footer-copy">© {{ date('Y') }} Panthera Custom Apparel.</span>
            <div class="footer-links">
                <a href="#koleksi">Koleksi</a>
                <a href="#proses">Cara kerja</a>
                <a href="{{ route('login') }}">Portal tim</a>
            </div>
        </div>
    </footer>
</body>
</html>
