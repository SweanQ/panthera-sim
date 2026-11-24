@extends('layouts.storefront')

@section('title', 'Buat pesanan — Panthera')

@push('styles')
<style>
    .cw-page-head { padding: 48px 0 25px; }
    .cw-page-head h1 { margin: 12px 0 8px; font: 600 clamp(34px, 5vw, 48px)/1.08 var(--cw-display); letter-spacing: -.06em; }
    .cw-page-head p { max-width: 610px; margin: 0; color: var(--cw-muted); font-size: 12px; line-height: 1.8; }
    .cw-order-layout { display: grid; grid-template-columns: minmax(0, 1fr) 300px; gap: 22px; align-items: start; padding-bottom: 65px; }
    .cw-form-card, .cw-estimate-card { padding: 24px; border: 1px solid var(--cw-line); border-radius: 9px; background: var(--cw-card); }
    .cw-order-form { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .cw-span-two { grid-column: 1/-1; }
    .cw-price-card { position: sticky; top: 96px; }
    .cw-price-card h2 { margin: 0 0 6px; font: 600 17px var(--cw-display); }
    .cw-price-card > p { margin: 0 0 20px; color: var(--cw-muted); font-size: 10px; line-height: 1.7; }
    .cw-price-line { display: flex; justify-content: space-between; gap: 12px; padding: 12px 0; border-top: 1px solid var(--cw-line); color: var(--cw-muted); font-size: 11px; }
    .cw-price-line strong { color: var(--cw-ink); }
    .cw-total-line { margin-top: 6px; padding-top: 15px; border-top: 1px solid #cfd2c8; }
    .cw-total-line strong { color: #35572b; font: 700 19px var(--cw-display); }
    .cw-disclaimer { margin-top: 17px; padding: 12px; border-radius: 5px; background: #edf2e6; color: #536247; font-size: 10px; line-height: 1.7; }
    .cw-form-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 5px; }
    @media (max-width: 760px) {
        .cw-order-layout { grid-template-columns: 1fr; }
        .cw-price-card { position: static; grid-row: 1; }
    }
    @media (max-width: 500px) {
        .cw-form-card, .cw-estimate-card { padding: 17px; }
        .cw-order-form { grid-template-columns: 1fr; }
        .cw-span-two { grid-column: auto; }
        .cw-form-actions { justify-content: stretch; flex-direction: column-reverse; }
        .cw-form-actions .cw-button { width: 100%; }
    }
</style>
@endpush

@section('content')
<header class="cw-page-head">
    <span class="cw-eyebrow">Pesanan baru</span>
    <h1>Mulai dari idemu.</h1>
    <p>Lengkapi detail produk dan kontak. Permintaan akan masuk ke tim kami untuk dikonfirmasi sebelum produksi—belum ada pembayaran di tahap ini.</p>
</header>

@if($errors->any())
    <div class="cw-error" role="alert" style="margin-bottom:18px">
        <strong>Periksa kembali data pesanan:</strong>
        <ul style="margin:7px 0 0;padding-left:18px">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<div class="cw-order-layout">
    <section class="cw-form-card">
        <form class="cw-order-form" id="order-form" method="POST" action="{{ route('shop.orders.store') }}">
            @csrf
            <div>
                <label class="cw-label" for="order-name">Nama / komunitas</label>
                <input class="cw-field" id="order-name" name="name" value="{{ old('name', auth()->user()?->role === 'customer' ? auth()->user()->name : '') }}" required maxlength="100" autocomplete="name" @readonly(auth()->user()?->role === 'customer')>
            </div>
            <div>
                <label class="cw-label" for="order-phone">Nomor WhatsApp</label>
                <input class="cw-field" id="order-phone" name="whatsapp_number" value="{{ old('whatsapp_number') }}" required maxlength="30" placeholder="08xx atau 62xx" autocomplete="tel">
                <div class="cw-help">Gunakan nomor aktif agar tim dapat mengonfirmasi pesanan.</div>
            </div>
            <div class="cw-span-two">
                <label class="cw-label" for="order-product">Produk</label>
                <select class="cw-field" id="order-product" name="product_id" required>
                    <option value="" data-price="0">Pilih produk</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->base_price }}" @selected(old('product_id', $selectedProductId) == $product->id)>
                            {{ $product->name }} — Rp {{ number_format((float) $product->base_price, 0, ',', '.') }} / item
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="cw-label" for="order-size">Ukuran</label>
                <input class="cw-field" id="order-size" name="size" value="{{ old('size', 'L') }}" placeholder="Contoh: S, M, L, XL" required maxlength="20">
            </div>
            <div>
                <label class="cw-label" for="order-color">Warna</label>
                <input class="cw-field" id="order-color" name="color" value="{{ old('color') }}" placeholder="Contoh: Hitam" required maxlength="50">
            </div>
            <div>
                <label class="cw-label" for="order-quantity">Jumlah (pcs)</label>
                <input class="cw-field" id="order-quantity" type="number" name="quantity" value="{{ old('quantity', 12) }}" min="1" max="5000" required>
            </div>
            <div>
                <label class="cw-label" for="order-notes">Catatan tambahan (opsional)</label>
                <input class="cw-field" id="order-notes" name="notes" value="{{ old('notes') }}" maxlength="2000" placeholder="Desain, tenggat, dll.">
            </div>
            <div class="cw-form-actions cw-span-two">
                <a class="cw-button cw-button-outline" href="{{ route('home') }}#koleksi">Kembali ke katalog</a>
                <button class="cw-button cw-button-dark" type="submit">Kirim permintaan <span aria-hidden="true">→</span></button>
            </div>
        </form>
    </section>

    <aside class="cw-estimate-card cw-price-card" aria-live="polite">
        <h2>Estimasi pesanan</h2>
        <p>Perkiraan biaya dihitung dari harga dasar produk dan jumlah yang dipilih.</p>
        <div class="cw-price-line"><span>Harga per item</span><strong id="unit-price">—</strong></div>
        <div class="cw-price-line"><span>Jumlah</span><strong id="quantity-preview">{{ old('quantity', 12) }} pcs</strong></div>
        <div class="cw-price-line cw-total-line"><span>Estimasi total</span><strong id="total-price">—</strong></div>
        <div class="cw-disclaimer">Harga akhir dan jadwal produksi akan dikonfirmasi tim Panthera setelah permintaan diterima.</div>
    </aside>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const product = document.getElementById('order-product');
    const quantity = document.getElementById('order-quantity');
    const unitPrice = document.getElementById('unit-price');
    const quantityPreview = document.getElementById('quantity-preview');
    const totalPrice = document.getElementById('total-price');
    const currency = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 });

    function updateEstimate() {
        const price = Number(product.selectedOptions[0]?.dataset.price || 0);
        const count = Math.max(0, Number(quantity.value || 0));
        unitPrice.textContent = price ? currency.format(price) : '—';
        quantityPreview.textContent = `${count} pcs`;
        totalPrice.textContent = price ? currency.format(price * count) : '—';
    }

    product.addEventListener('change', updateEstimate);
    quantity.addEventListener('input', updateEstimate);
    updateEstimate();
})();
</script>
@endpush
