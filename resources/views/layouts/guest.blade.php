<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Panthera — Portal Tim</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { margin: 0; background: #10130f; color: #20251f; font-family: Figtree, sans-serif; }
        .guest-shell { min-height: 100vh; display: grid; grid-template-columns: minmax(0, 1fr) minmax(380px, .8fr); }
        .guest-story { position: relative; display: flex; min-height: 100vh; flex-direction: column; justify-content: space-between; overflow: hidden; padding: 38px clamp(28px, 6vw, 88px); background: linear-gradient(140deg, rgba(16,19,15,.95), rgba(16,19,15,.48)), url('https://images.unsplash.com/photo-1529139574466-a303027c1d8b?auto=format&fit=crop&w=1400&q=85') center/cover; color: white; }
        .guest-brand { display: flex; align-items: center; gap: 12px; color: white; font-size: 13px; font-weight: 800; letter-spacing: .13em; text-decoration: none; }
        .guest-brand svg { width: 38px; height: 38px; }
        .guest-message { position: relative; z-index: 1; max-width: 500px; padding-block: 70px; }
        .guest-message span { color: #c5f36a; font-size: 10px; font-weight: 700; letter-spacing: .17em; text-transform: uppercase; }
        .guest-message h1 { margin: 16px 0; font: 600 clamp(38px, 5vw, 64px)/1.05 Manrope, sans-serif; letter-spacing: -.06em; }
        .guest-message p, .guest-copy { color: rgba(255,255,255,.68); font-size: 12px; line-height: 1.8; }
        .guest-panel { display: flex; align-items: center; justify-content: center; padding: 35px; background: #f5f5f0; }
        .guest-card { width: min(420px, 100%); }
        .guest-card > a { display: none; }
        .guest-heading { margin-bottom: 25px; }
        .guest-heading h2 { margin: 0 0 7px; color: #171a16; font: 700 27px Manrope, sans-serif; letter-spacing: -.04em; }
        .guest-heading p { margin: 0; color: #777c73; font-size: 12px; }
        .guest-card input:not([type=checkbox]) { min-height: 43px; border-color: #dfe1d8; border-radius: 5px; background: #fffefa; }
        .guest-card button[type=submit] { min-height: 42px; border: 0; border-radius: 5px; background: #233a26; color: white; font-weight: 700; }
        .guest-card a { color: #526b3e; }
        @media (max-width: 760px) {
            .guest-shell { grid-template-columns: 1fr; }
            .guest-story { min-height: auto; padding: 20px 24px; }
            .guest-message { padding: 35px 0 25px; }
            .guest-message h1 { font-size: 37px; }
            .guest-copy { display: none; }
            .guest-panel { padding: 30px 22px 45px; }
        }
    </style>
    </head>
    <body class="font-sans antialiased">
        <div class="guest-shell">
            <section class="guest-story">
                <a class="guest-brand" href="{{ route('home') }}">
                    <x-application-logo />
                    <span>PANTHERA</span>
                </a>
                <div class="guest-message">
                    <span>Panthera custom apparel</span>
                    @if(request()->routeIs('register'))
                        <h1>Pesanan personal, pengalaman lebih mudah.</h1>
                        <p>Buat akun pelanggan untuk mengirim pesanan dan melihat riwayat yang terhubung ke akunmu.</p>
                    @else
                        <h1>Semua pesanan, satu tempat.</h1>
                        <p>Masuk sebagai pelanggan untuk melihat riwayat, atau sebagai tim untuk mengelola produksi Panthera.</p>
                    @endif
                </div>
                <p class="guest-copy">Panthera · Apparel dibuat bersama.</p>
            </section>
            <main class="guest-panel">
                <div class="guest-card">
                    <div class="guest-heading">
                        @if(request()->routeIs('register'))
                            <h2>Buat akun pelanggan</h2>
                            <p>Akun baru digunakan untuk memesan dan melihat riwayat.</p>
                        @else
                            <h2>Selamat datang</h2>
                            <p>Masuk ke akun Panthera.</p>
                        @endif
                    </div>
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
