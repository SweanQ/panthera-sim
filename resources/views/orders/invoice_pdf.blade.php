<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->order_code }}</title>
    <style>
        body { font-family: sans-serif; font-size: 13px; color: #222; }
        .header { border-bottom: 2px solid #111; padding-bottom: 10px; margin-bottom: 20px; }
        .title { font-size: 20px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">PANTHERA CUSTOM APPAREL</div>
        <div>Nota Pemesanan Resmi (Invoice) — {{ $order->order_code }}</div>
    </div>

    <p>
        <strong>Pelanggan:</strong> {{ $order->customer->name }} ({{ $order->customer->whatsapp_number }})<br>
        <strong>Alamat:</strong> {{ $order->customer->address ?? '-' }}<br>
        <strong>Tanggal Cetak:</strong> {{ now()->format('d M Y H:i') }} | <strong>Dicatat oleh:</strong> {{ $order->user?->name ?? 'Pelanggan' }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Spesifikasi</th>
                <th class="right">Harga Satuan</th>
                <th class="right">Qty</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $order->product->name }}</td>
                <td>Ukuran: {{ $order->size }}, Warna: {{ $order->color }}{{ $order->notes ? ', Catatan: ' . $order->notes : '' }}</td>
                <td class="right">Rp {{ number_format($order->product->base_price, 0, ',', '.') }}</td>
                <td class="right">{{ $order->quantity }}</td>
                <td class="right">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    @php
        $paid = $order->payments->sum('amount');
        $remaining = max(0, $order->total_price - $paid);
    @endphp

    <table>
        <tr>
            <th>Total Tagihan</th>
            <td class="right"><strong>Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <th>Total Dibayarkan (DP / Pelunasan)</th>
            <td class="right">Rp {{ number_format($paid, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Sisa Pelunasan</th>
            <td class="right"><strong>Rp {{ number_format($remaining, 0, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <th>Status Pembayaran & Produksi</th>
            <td class="right">{{ strtoupper(str_replace('_', ' ', $order->payment_status)) }} / {{ strtoupper(str_replace('_', ' ', $order->production_status)) }}</td>
        </tr>
    </table>

    <p style="margin-top: 25px; font-size: 11px; color: #555;">
        * Fasilitas gratis revisi desain maksimal 3 kali sebelum proses produksi dimulai.<br>
        * Pelunasan dilakukan saat pesanan selesai diproduksi.
    </p>
</body>
</html>