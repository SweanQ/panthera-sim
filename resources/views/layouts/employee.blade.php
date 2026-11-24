<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Karyawan') — Panthera</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: "DM Sans", sans-serif; background: #f3f5f0; color: #20271f; }
        h1, h2, h3 { font-family: "Manrope", sans-serif; }
    </style>
</head>
<body class="min-h-screen antialiased">
    <div class="min-h-screen lg:grid lg:grid-cols-[250px_minmax(0,1fr)]">
        <aside class="bg-[#15231b] px-5 py-6 text-white lg:min-h-screen">
            <a href="{{ route('employee.dashboard') }}" class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-lime-300 text-lg font-extrabold text-[#15231b]">P</span>
                <span>
                    <span class="block text-sm font-extrabold tracking-[.18em]">PANTHERA</span>
                    <span class="block text-[10px] uppercase tracking-[.2em] text-lime-200">Employee workspace</span>
                </span>
            </a>

            <nav class="mt-8 grid grid-cols-2 gap-2 text-sm lg:grid-cols-1">
                <a href="{{ route('employee.dashboard') }}" class="rounded-lg px-3 py-2.5 {{ request()->routeIs('employee.dashboard') ? 'bg-white/10 text-lime-200' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">Ringkasan kerja</a>
                <a href="{{ route('employee.orders.index') }}" class="rounded-lg px-3 py-2.5 {{ request()->routeIs('employee.orders.*') ? 'bg-white/10 text-lime-200' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">Pesanan produksi</a>
                <a href="{{ route('inventory.apparel') }}" class="rounded-lg px-3 py-2.5 {{ request()->routeIs('inventory.apparel*') ? 'bg-white/10 text-lime-200' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">Stok baju</a>
                <a href="{{ route('inventory.materials') }}" class="rounded-lg px-3 py-2.5 {{ request()->routeIs('inventory.materials*') ? 'bg-white/10 text-lime-200' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">Stok bahan</a>
                <a href="{{ route('inventory.history') }}" class="rounded-lg px-3 py-2.5 {{ request()->routeIs('inventory.history') ? 'bg-white/10 text-lime-200' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">Riwayat stok</a>
                <a href="{{ route('inventory.catalog') }}" class="rounded-lg px-3 py-2.5 {{ request()->routeIs('inventory.catalog*') ? 'bg-white/10 text-lime-200' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">Katalog produk</a>
            </nav>

            <div class="mt-8 hidden rounded-xl border border-white/10 bg-white/5 p-4 lg:block">
                <p class="text-xs font-semibold text-lime-200">Catatan stok</p>
                <p class="mt-1 text-xs leading-5 text-stone-300">Perubahan stok dicatat manual dan masuk ke riwayat. Pesanan tidak mengurangi stok otomatis.</p>
            </div>
        </aside>

        <div class="min-w-0">
            <header class="flex min-h-[72px] items-center justify-between border-b border-stone-200 bg-white px-5 sm:px-8">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[.18em] text-emerald-800">Panthera / Karyawan</p>
                    <p class="mt-0.5 text-sm font-semibold text-stone-800">{{ auth()->user()->name }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="hidden text-xs font-semibold text-stone-600 hover:text-emerald-800 sm:inline">Lihat toko</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="rounded-lg border border-stone-200 px-3 py-2 text-xs font-bold text-stone-700 hover:bg-stone-50" type="submit">Keluar</button>
                    </form>
                </div>
            </header>

            <main class="mx-auto max-w-7xl px-4 py-6 sm:px-8 sm:py-8">
                @if (session('success'))
                    <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900" role="status">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900" role="alert">
                        <p class="font-bold">Data belum dapat disimpan</p>
                        <ul class="mt-1 list-inside list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
