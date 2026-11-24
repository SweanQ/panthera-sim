<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#10130f">
    <meta name="description" content="Panthera — apparel custom untuk tim, komunitas, dan brand. Pilih produk, kirim kebutuhan, lalu pantau pesanan dengan mudah.">
    <title>@yield('title', 'Panthera — Apparel untuk ceritamu')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --cw-ink: #171a16;
            --cw-muted: #777c73;
            --cw-paper: #f5f5f0;
            --cw-card: #fffefa;
            --cw-line: #e5e6de;
            --cw-green: #c5f36a;
            --cw-green-dark: #233a26;
            --cw-dark: #10130f;
            --cw-font: "DM Sans", sans-serif;
            --cw-display: "Manrope", sans-serif;
        }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { margin: 0; background: var(--cw-paper); color: var(--cw-ink); font-family: var(--cw-font); -webkit-font-smoothing: antialiased; }
        a { color: inherit; text-decoration: none; }
        button, input, select, textarea { font: inherit; }
        button, a { -webkit-tap-highlight-color: transparent; }
        .cw-shell { width: min(1180px, calc(100% - 48px)); margin-inline: auto; }
        .cw-header { position: sticky; top: 0; z-index: 20; border-bottom: 1px solid rgba(255,255,255,.09); background: rgba(16,19,15,.96); color: white; backdrop-filter: blur(16px); }
        .cw-nav { min-height: 74px; display: flex; align-items: center; justify-content: space-between; gap: 20px; }
        .cw-brand { display: inline-flex; align-items: center; gap: 11px; flex-shrink: 0; }
        .cw-mark { width: 36px; height: 36px; display: grid; place-items: center; border-radius: 11px; background: var(--cw-green); color: var(--cw-dark); font: 800 17px var(--cw-display); letter-spacing: -.08em; }
        .cw-brand-name { font: 800 14px var(--cw-display); letter-spacing: .13em; }
        .cw-brand-name span { color: var(--cw-green); }
        .cw-nav-links { display: flex; align-items: center; gap: 28px; }
        .cw-nav-links a { color: #b7bcb3; font-size: 12px; transition: color .18s ease; }
        .cw-nav-links a:hover { color: white; }
        .cw-nav-links .cw-nav-auth { min-height: 40px; display: inline-flex; align-items: center; justify-content: center; padding: 0 14px; border: 1px solid #687066; border-radius: 4px; color: white; font-size: 12px; font-weight: 700; transition: background .18s ease, border-color .18s ease; }
        .cw-nav-links .cw-nav-auth:hover { border-color: white; background: rgba(255,255,255,.08); }
        .cw-nav-links .cw-nav-register { border-color: var(--cw-green); background: var(--cw-green); color: var(--cw-dark); }
        .cw-nav-links .cw-nav-register:hover { border-color: #d7ff8b; background: #d7ff8b; color: var(--cw-dark); }
        .cw-nav-links .cw-nav-cta { padding: 11px 15px; border-radius: 4px; background: var(--cw-green); color: var(--cw-dark); font-weight: 700; }
        .cw-nav-links .cw-nav-cta:hover { background: #d7ff8b; }
        .cw-nav-logout { min-height: 40px; display: inline-flex; align-items: center; justify-content: center; padding: 0 14px; border: 1px solid #a8423b; border-radius: 4px; background: #8f302b; color: white; font-size: 12px; font-weight: 700; cursor: pointer; transition: background .18s ease, border-color .18s ease; }
        .cw-nav-logout:hover { border-color: #e17a70; background: #a63b34; }
        .cw-main { min-height: 65vh; }
        .cw-flash { margin-top: 20px; padding: 13px 16px; border: 1px solid #b8d78e; border-radius: 8px; background: #edf7df; color: #2e542b; font-size: 13px; }
        .cw-error { padding: 12px 14px; border: 1px solid #edc8bd; border-radius: 8px; background: #fff0ec; color: #8d3926; font-size: 13px; }
        .cw-footer { margin-top: 50px; border-top: 1px solid var(--cw-line); background: var(--cw-paper); }
        .cw-footer-inner { min-height: 92px; display: flex; align-items: center; justify-content: space-between; gap: 20px; }
        .cw-footer .cw-brand-name { color: var(--cw-ink); }
        .cw-footer small { color: var(--cw-muted); font-size: 11px; }
        .cw-footer-links { display: flex; gap: 18px; color: var(--cw-muted); font-size: 11px; }
        .cw-footer-links a:hover { color: var(--cw-ink); }
        .cw-button { min-height: 46px; display: inline-flex; align-items: center; justify-content: center; gap: 10px; padding: 0 17px; border: 1px solid var(--cw-green); border-radius: 5px; background: var(--cw-green); color: var(--cw-dark); font-size: 12px; font-weight: 700; cursor: pointer; transition: transform .18s ease, background .18s ease; }
        .cw-button:hover { transform: translateY(-2px); background: #d7ff8b; }
        .cw-button-dark { border-color: var(--cw-dark); background: var(--cw-dark); color: white; }
        .cw-button-dark:hover { background: #30362e; }
        .cw-button-outline { border-color: var(--cw-line); background: transparent; color: var(--cw-ink); }
        .cw-button-outline:hover { background: #eceee6; }
        .cw-eyebrow { display: inline-flex; align-items: center; gap: 9px; color: #526b3e; font-size: 10px; font-weight: 700; letter-spacing: .17em; text-transform: uppercase; }
        .cw-eyebrow::before { width: 22px; height: 1px; background: currentColor; content: ""; }
        .cw-field { width: 100%; min-height: 46px; padding: 11px 13px; border: 1px solid #dfe1d8; border-radius: 5px; outline: none; background: white; color: var(--cw-ink); font-size: 13px; }
        .cw-field:focus { border-color: #708d55; box-shadow: 0 0 0 3px rgba(112,141,85,.12); }
        .cw-label { display: block; margin-bottom: 7px; color: #4b5148; font-size: 11px; font-weight: 700; }
        .cw-help { margin-top: 6px; color: var(--cw-muted); font-size: 10px; line-height: 1.6; }

        @media (max-width: 700px) {
            .cw-shell { width: min(100% - 34px, 540px); }
            .cw-nav { min-height: 66px; flex-wrap: wrap; padding-block: 10px; }
            .cw-nav-links { flex: 1 1 100%; flex-wrap: wrap; justify-content: flex-start; gap: 8px 14px; }
            .cw-nav-links a:not(.cw-nav-cta):not(.cw-nav-auth):not(.cw-nav-member) { display: none; }
            .cw-nav-links .cw-nav-member { font-size: 10px; white-space: nowrap; }
            .cw-nav-links .cw-nav-auth { min-height: 36px; padding: 0 9px; font-size: 10px; }
            .cw-nav-logout { min-height: 36px; padding: 0 10px; font-size: 10px; }
            .cw-nav-links .cw-nav-cta { padding: 10px 12px; font-size: 11px; }
            .cw-footer-inner { align-items: flex-start; flex-direction: column; padding-block: 22px; }
            .cw-footer-links { flex-wrap: wrap; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <header class="cw-header">
        <nav class="cw-shell cw-nav" aria-label="Navigasi utama">
            <a class="cw-brand" href="{{ route('home') }}" aria-label="Panthera, beranda">
                <span class="cw-mark">P</span>
                <span class="cw-brand-name">PANTHERA<span>®</span></span>
            </a>
            <div class="cw-nav-links">
                @auth
                    <a class="cw-nav-member" href="{{ route('home') }}#koleksi">Katalog</a>
                    <a class="cw-nav-member" href="{{ route('home') }}#cara-kerja">Cara kerja</a>
                    <a class="cw-nav-member" href="{{ route('shop.orders.track') }}">Lacak pesanan</a>
                    @if(auth()->user()->role === 'customer')
                        <a href="{{ route('customer.orders') }}">Riwayat pesanan</a>
                    @elseif(auth()->user()->role === 'pegawai')
                        <a href="{{ route('employee.dashboard') }}">Portal karyawan</a>
                    @else
                        <a href="{{ route('dashboard') }}">Portal tim</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="cw-nav-logout" type="submit">Keluar</button>
                    </form>
                @else
                    <a class="cw-nav-auth" href="{{ route('login') }}">Masuk</a>
                    <a class="cw-nav-auth cw-nav-register" href="{{ route('register') }}">Daftar</a>
                @endauth
                @auth
                    @unless(request()->routeIs('home'))
                        <a class="cw-nav-cta" href="{{ route('shop.orders.create') }}">Buat pesanan <span aria-hidden="true">↗</span></a>
                    @endunless
                @endauth
            </div>
        </nav>
    </header>
    <main class="cw-main">
        <div class="cw-shell">
            @if (session('success'))
                <div class="cw-flash" role="status">{{ session('success') }}</div>
            @endif
            @yield('content')
        </div>
    </main>
    <footer class="cw-footer">
        <div class="cw-shell cw-footer-inner">
            <a class="cw-brand" href="{{ route('home') }}">
                <span class="cw-mark">P</span>
                <span class="cw-brand-name">PANTHERA<span>®</span></span>
            </a>
            <small>© {{ date('Y') }} Panthera. Apparel dibuat bersama.</small>
            <div class="cw-footer-links">
                <a href="{{ route('home') }}#koleksi">Katalog</a>
                <a href="{{ route('home') }}#inquiry">Konsultasi custom</a>
                @auth
                    @if(auth()->user()->role === 'customer')
                        <a href="{{ route('customer.orders') }}">Riwayat pesanan</a>
                    @elseif(auth()->user()->role === 'pegawai')
                        <a href="{{ route('employee.dashboard') }}">Portal karyawan</a>
                    @else
                        <a href="{{ route('dashboard') }}">Portal tim</a>
                    @endif
                @else
                    <a href="{{ route('login') }}">Masuk</a>
                @endauth
            </div>
        </div>
    </footer>
    @stack('scripts')
</body>
</html>
