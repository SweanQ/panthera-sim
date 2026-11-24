@extends('layouts.panthera')

@section('content')
<div class="space-y-7">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[.18em] text-lime-300">Peluang baru</p>
            <h1 class="mt-2 text-2xl font-semibold tracking-tight text-zinc-900">Permintaan custom</h1>
            <p class="mt-1 text-sm text-stone-600">Tinjau konsultasi dari calon pelanggan dan catat tindak lanjutnya.</p>
        </div>
        <a href="{{ route('orders.index') }}" class="text-sm text-stone-600 transition hover:text-zinc-900">← Kembali ke pesanan</a>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-emerald-800 bg-emerald-950/60 p-4 text-sm text-emerald-300">{{ session('success') }}</div>
    @endif

    <section class="overflow-hidden rounded-xl border border-zinc-800 bg-zinc-900/70">
        <div class="flex items-center justify-between border-b border-zinc-800 p-5">
            <h2 class="font-semibold text-white">Kotak masuk</h2>
            <span class="text-xs text-white">{{ $inquiries->total() }} permintaan</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-800 text-sm">
                <thead class="bg-zinc-950/60 text-left text-xs uppercase tracking-wider text-white">
                    <tr>
                        <th class="px-5 py-3">Diterima</th>
                        <th class="px-5 py-3">Pelanggan</th>
                        <th class="px-5 py-3">Kebutuhan</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Tindak lanjut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/80">
                    @forelse($inquiries as $inquiry)
                        <tr class="align-top hover:bg-zinc-800/30">
                            <td class="whitespace-nowrap px-5 py-4 text-xs text-white">{{ $inquiry->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-5 py-4">
                                <div class="font-medium text-white">{{ $inquiry->name }}</div>
                                <a class="mt-1 inline-block text-xs text-white underline hover:text-zinc-300" href="https://wa.me/{{ $inquiry->whatsapp_number }}" target="_blank" rel="noopener noreferrer">
                                    WhatsApp · {{ $inquiry->whatsapp_number }} ↗
                                </a>
                            </td>
                            <td class="min-w-64 px-5 py-4">
                                <div class="font-medium text-white">{{ $inquiry->product?->name ?? 'Produk custom' }}@if($inquiry->quantity) <span class="text-white">· {{ $inquiry->quantity }} pcs</span>@endif</div>
                                <p class="mt-1 whitespace-pre-line text-xs leading-5 text-white">{{ $inquiry->details }}</p>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4">
                                <span class="rounded-full border border-zinc-700 bg-zinc-950 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-white">{{ $inquiry->status }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <form action="{{ route('inquiries.updateStatus', $inquiry) }}" method="POST" class="flex min-w-48 items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="min-w-0 flex-1 rounded-md border border-zinc-700 bg-zinc-950 px-2 py-2 text-xs text-white">
                                        @foreach(['baru' => 'Baru', 'dihubungi' => 'Sudah dihubungi', 'selesai' => 'Selesai'] as $value => $label)
                                            <option value="{{ $value }}" @selected($inquiry->status === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <button class="rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2 text-xs font-semibold text-white hover:bg-zinc-800" type="submit">Simpan</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-14 text-center text-sm text-white">Belum ada permintaan konsultasi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($inquiries->hasPages())
            <div class="border-t border-zinc-800 p-4">{{ $inquiries->links() }}</div>
        @endif
    </section>
</div>
@endsection
