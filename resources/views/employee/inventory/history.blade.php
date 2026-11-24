@extends(auth()->user()->role === 'admin' ? 'layouts.panthera' : 'layouts.employee')

@section('title', 'Riwayat stok')

@section('content')
    @php($isAdmin = auth()->user()->role === 'admin')
    @php($rowBorder = $isAdmin ? 'border-b border-zinc-700' : 'border-b border-stone-200')

    <div>
        <p class="text-xs font-bold uppercase tracking-[.18em] text-emerald-800">Audit persediaan</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-stone-900">Riwayat stok</h1>
        <p class="mt-2 text-sm text-stone-600">100 pergerakan terbaru untuk stok baju dan bahan baku, termasuk pelaku dan alasannya.</p>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse text-left text-sm {{ $isAdmin ? 'bg-black text-white' : '' }}">
                <thead class="{{ $isAdmin ? 'bg-black text-xs uppercase tracking-wide text-white' : 'bg-stone-50 text-xs uppercase tracking-wide text-stone-500' }}">
                    <tr class="{{ $rowBorder }}"><th class="px-5 py-3">Waktu</th><th class="px-5 py-3">Jenis</th><th class="px-5 py-3">Item</th><th class="px-5 py-3">Pergerakan</th><th class="px-5 py-3">Saldo</th><th class="px-5 py-3">Alasan / petugas</th></tr>
                </thead>
                <tbody class="{{ $isAdmin ? 'divide-y divide-zinc-700' : 'divide-y divide-stone-200' }}">
                    @forelse($movements as $movement)
                        <tr>
                            <td class="whitespace-nowrap px-5 py-4 text-xs {{ $isAdmin ? 'text-white' : 'text-stone-600' }}">{{ $movement['created_at']->format('d/m/Y H:i') }}</td>
                            <td class="px-5 py-4"><span class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $isAdmin ? 'border border-zinc-700 bg-zinc-900 text-white' : 'bg-stone-100 text-stone-700' }}">{{ $movement['category'] }}</span></td>
                            <td class="px-5 py-4 font-semibold {{ $isAdmin ? 'text-white' : 'text-stone-900' }}">{{ $movement['item'] }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-xs font-bold {{ $isAdmin ? 'text-white' : ($movement['type'] === 'masuk' ? 'text-emerald-800' : 'text-rose-800') }}">{{ strtoupper($movement['type']) }} {{ $movement['quantity'] }} {{ $movement['unit'] }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-xs {{ $isAdmin ? 'text-white' : 'text-stone-700' }}">{{ $movement['before'] }} → {{ $movement['after'] }} {{ $movement['unit'] }}</td>
                            <td class="px-5 py-4">
                                <p class="text-xs {{ $isAdmin ? 'text-white' : 'text-stone-700' }}">{{ $movement['reason'] }}</p>
                                <p class="mt-1 text-[11px] {{ $isAdmin ? 'text-white' : 'text-stone-500' }}">{{ $movement['user'] }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="px-5 py-10 text-center text-sm {{ $isAdmin ? 'text-white' : 'text-stone-500' }}" colspan="6">Belum ada pergerakan stok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
