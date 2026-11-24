@extends('layouts.panthera')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-zinc-900">Catat Pesanan Baru</h1>
            <p class="text-sm text-stone-600 mt-1">Input detail pesanan apparel dan pembayaran uang muka (minimal DP 50%).</p>
        </div>
        <a href="{{ route('orders.index') }}" class="text-sm text-stone-600 hover:text-zinc-900">&larr; Kembali</a>
    </div>

    <div class="rounded-xl bg-zinc-900/70 border border-zinc-800/90 p-6 sm:p-8">
        @if($errors->any())
            <div class="mb-6 p-4 rounded-lg bg-red-950/60 border border-red-800/80 text-red-300 text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Mode Pelanggan -->
            <div>
                <label class="block text-xs font-medium uppercase tracking-wider text-zinc-400 mb-2">Data Pelanggan</label>
                <div class="grid grid-cols-2 gap-2 mb-3">
                    <label class="flex items-center justify-center gap-2 p-2.5 rounded-lg border border-zinc-800 bg-zinc-950 cursor-pointer text-xs font-medium">
                        <input type="radio" name="customer_mode" value="existing" checked onchange="toggleCustomerMode(this.value)">
                        <span>Pelanggan Terdaftar</span>
                    </label>
                    <label class="flex items-center justify-center gap-2 p-2.5 rounded-lg border border-zinc-800 bg-zinc-950 cursor-pointer text-xs font-medium">
                        <input type="radio" name="customer_mode" value="new" onchange="toggleCustomerMode(this.value)">
                        <span>+ Pelanggan Baru</span>
                    </label>
                </div>

                <div id="existingCustomerBox">
                    <select name="customer_id" class="w-full rounded-lg bg-zinc-950 border border-zinc-800 text-zinc-100 text-sm px-3.5 py-2.5 focus:outline-none focus:border-white">
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->whatsapp_number }})</option>
                        @endforeach
                    </select>
                </div>

                <div id="newCustomerBox" class="hidden space-y-3 p-4 rounded-lg bg-zinc-950 border border-zinc-800">
                    <div>
                        <input type="text" name="new_customer_name" placeholder="Nama Pelanggan / Instansi"
                               class="w-full rounded-lg bg-zinc-900 border border-zinc-800 text-zinc-100 text-sm px-3.5 py-2 focus:outline-none focus:border-white">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <input type="text" name="new_whatsapp_number" placeholder="No. WhatsApp (cth: 62812...)"
                               class="w-full rounded-lg bg-zinc-900 border border-zinc-800 text-zinc-100 text-sm px-3.5 py-2 focus:outline-none focus:border-white">
                        <input type="text" name="new_address" placeholder="Alamat Singkat"
                               class="w-full rounded-lg bg-zinc-900 border border-zinc-800 text-zinc-100 text-sm px-3.5 py-2 focus:outline-none focus:border-white">
                    </div>
                </div>
            </div>

            <!-- Produk -->
            <div>
                <label class="block text-xs font-medium uppercase tracking-wider text-zinc-400 mb-2">Jenis Produk Apparel</label>
                <select id="productSelect" name="product_id" onchange="calculateEstimate()" required
                        class="w-full rounded-lg bg-zinc-950 border border-zinc-800 text-zinc-100 text-sm px-3.5 py-2.5 focus:outline-none focus:border-white">
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" data-price="{{ $p->base_price }}">
                            {{ $p->name }} — Rp {{ number_format($p->base_price, 0, ',', '.') }}/pcs (Stok {{ strtoupper($p->stock_source) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Spesifikasi -->
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium uppercase tracking-wider text-zinc-400 mb-2">Ukuran</label>
                    <input type="text" name="size" value="{{ old('size', 'L') }}" required
                           class="w-full rounded-lg bg-zinc-950 border border-zinc-800 text-zinc-100 text-sm px-3.5 py-2.5 focus:outline-none focus:border-white">
                </div>
                <div>
                    <label class="block text-xs font-medium uppercase tracking-wider text-zinc-400 mb-2">Warna</label>
                    <input type="text" name="color" value="{{ old('color', 'Hitam') }}" required
                           class="w-full rounded-lg bg-zinc-950 border border-zinc-800 text-zinc-100 text-sm px-3.5 py-2.5 focus:outline-none focus:border-white">
                </div>
                <div>
                    <label class="block text-xs font-medium uppercase tracking-wider text-zinc-400 mb-2">Kuantitas (pcs)</label>
                    <input type="number" id="qtyInput" name="quantity" value="{{ old('quantity', 10) }}" min="1" oninput="calculateEstimate()" required
                           class="w-full rounded-lg bg-zinc-950 border border-zinc-800 text-zinc-100 text-sm px-3.5 py-2.5 focus:outline-none focus:border-white">
                </div>
            </div>

            <!-- Box Estimasi Harga Real-time -->
            <div class="p-4 rounded-lg bg-zinc-950 border border-zinc-800 flex items-center justify-between text-sm">
                <div>
                    <div class="text-zinc-400 text-xs">Estimasi Total Tagihan</div>
                    <div id="totalPreview" class="text-lg font-semibold text-white">Rp 850.000</div>
                </div>
                <div class="text-right">
                    <div class="text-zinc-400 text-xs">Syarat Minimal DP (50%)</div>
                    <div id="minDpPreview" class="text-base font-semibold text-amber-400">Rp 425.000</div>
                </div>
            </div>

            <!-- Pembayaran -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium uppercase tracking-wider text-zinc-400 mb-2">Nominal DP Dibayarkan (Rp)</label>
                    <input type="number" id="dpInput" name="dp_amount" value="{{ old('dp_amount', 425000) }}" required
                           class="w-full rounded-lg bg-zinc-950 border border-zinc-800 text-zinc-100 text-sm px-3.5 py-2.5 focus:outline-none focus:border-white">
                </div>
                <div>
                    <label class="block text-xs font-medium uppercase tracking-wider text-zinc-400 mb-2">Metode Pembayaran</label>
                    <select name="payment_method" class="w-full rounded-lg bg-zinc-950 border border-zinc-800 text-zinc-100 text-sm px-3.5 py-2.5 focus:outline-none focus:border-white">
                        <option value="transfer">Transfer Bank</option>
                        <option value="tunai">Tunai</option>
                    </select>
                </div>
            </div>

            <!-- Upload Desain -->
            <div>
                <label class="block text-xs font-medium uppercase tracking-wider text-zinc-400 mb-2">Mockup / Referensi Desain (Opsional)</label>
                <input type="file" name="design_file" class="w-full text-xs text-zinc-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-zinc-800 file:text-zinc-200 hover:file:bg-zinc-700">
            </div>

            <!-- Submit -->
            <div class="pt-4 border-t border-zinc-800 flex items-center justify-end gap-3">
                <a href="{{ route('orders.index') }}" class="px-4 py-2.5 rounded-lg text-sm font-medium bg-zinc-900 text-zinc-300 border border-zinc-800 hover:bg-zinc-800">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-lg text-sm font-semibold bg-white text-black hover:bg-zinc-200 transition">
                    Simpan & Terbitkan Invoice
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleCustomerMode(mode) {
    document.getElementById('existingCustomerBox').classList.toggle('hidden', mode !== 'existing');
    document.getElementById('newCustomerBox').classList.toggle('hidden', mode !== 'new');
}

function calculateEstimate() {
    const select = document.getElementById('productSelect');
    const price = parseFloat(select.options[select.selectedIndex].dataset.price || 0);
    const qty = parseInt(document.getElementById('qtyInput').value || 0);
    const total = price * qty;
    const minDp = total * 0.5;

    document.getElementById('totalPreview').innerText = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('minDpPreview').innerText = 'Rp ' + minDp.toLocaleString('id-ID');
}
</script>
@endsection