<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Panthera — Portal Operasional</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #fff; color: #27272a; }
        .admin-header { border-bottom: 1px solid rgba(255,255,255,.09); background: rgba(16,19,15,.96); color: white; }
        .orders-table-scroll { scrollbar-width: none; -ms-overflow-style: none; }
        .orders-table-scroll::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="min-h-screen bg-white text-zinc-900 antialiased selection:bg-lime-200 selection:text-zinc-900">
    <!-- Top Navigation Bar -->
    <nav class="admin-header sticky top-0 z-30 backdrop-blur">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-8">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-lime-300 text-black font-bold flex items-center justify-center text-base">P</span>
                    <span class="font-semibold tracking-tight text-white text-base">PANTHERA <span class="text-lime-300 font-normal text-xs ml-1">OPS</span></span>
                </a>

                <div class="hidden sm:flex items-center gap-1">
                    <a href="{{ route('orders.index') }}"
                       class="px-3 py-1.5 rounded-md text-sm font-medium transition {{ request()->routeIs('dashboard', 'orders.*') ? 'bg-zinc-900 text-white border border-zinc-800' : 'text-white hover:bg-zinc-900' }}">
                        Pesanan & Produksi
                    </a>
                    @if(auth()->user()->role === 'admin')
                        <details class="group relative">
                            <summary class="cursor-pointer list-none rounded-md px-3 py-1.5 text-sm font-medium text-white transition hover:bg-zinc-900 {{ request()->routeIs('inventory.*') ? 'bg-zinc-900 text-white' : '' }}">
                                Inventaris <span aria-hidden="true" class="ml-1 text-[10px]">▼</span>
                            </summary>
                            <div class="absolute left-0 top-full z-40 mt-2 min-w-44 rounded-lg border border-zinc-800 bg-zinc-950 p-1.5 shadow-xl">
                                <a class="block rounded-md px-3 py-2 text-xs text-zinc-300 hover:bg-zinc-900 hover:text-white" href="{{ route('inventory.apparel') }}">Stok baju</a>
                                <a class="block rounded-md px-3 py-2 text-xs text-zinc-300 hover:bg-zinc-900 hover:text-white" href="{{ route('inventory.materials') }}">Stok bahan</a>
                                <a class="block rounded-md px-3 py-2 text-xs text-zinc-300 hover:bg-zinc-900 hover:text-white" href="{{ route('inventory.history') }}">Riwayat stok</a>
                                <a class="block rounded-md px-3 py-2 text-xs text-zinc-300 hover:bg-zinc-900 hover:text-white" href="{{ route('inventory.catalog') }}">Katalog produk</a>
                            </div>
                        </details>
                        <a href="{{ route('inquiries.index') }}"
                           class="px-3 py-1.5 rounded-md text-sm font-medium transition {{ request()->routeIs('inquiries.*') ? 'bg-zinc-900 text-white border border-zinc-800' : 'text-white hover:bg-zinc-900' }}">
                            Permintaan custom
                        </a>
                        <a href="{{ route('reports.index') }}"
                           class="px-3 py-1.5 rounded-md text-sm font-medium transition {{ request()->routeIs('reports.*') ? 'bg-zinc-900 text-white border border-zinc-800' : 'text-white hover:bg-zinc-900' }}">
                            Laporan Keuangan
                        </a>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('home') }}" class="hidden md:inline text-xs text-white hover:text-lime-300">Lihat toko ↗</a>
                <div class="text-right hidden sm:block">
                    <div class="text-xs font-medium text-zinc-200">{{ auth()->user()->name }}</div>
                    <div class="text-[11px] uppercase tracking-wider text-white">{{ auth()->user()->role }}</div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 rounded-md text-xs font-semibold bg-zinc-900 hover:bg-zinc-800 text-zinc-300 border border-zinc-800 transition">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>
</body>
</html>