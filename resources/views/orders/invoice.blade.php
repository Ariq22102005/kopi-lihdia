<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $order->order_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f1de; }
        .invoice { background: #fff; max-width: 800px; margin: 24px auto; padding: 40px; border-radius: 16px; }
        .brand { color: #0b5d2e; font-weight: 800; font-size: 1.6rem; }
        @media print {
            body { background: #fff; }
            .invoice { margin: 0; padding: 0; border-radius: 0; max-width: 100%; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
<div class="invoice">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div class="brand">☕ {{ config('app.name') }}</div>
            <div class="text-muted small">Invoice pesanan</div>
        </div>
        <div class="text-end">
            <div class="fw-bold">{{ $order->order_number }}</div>
            <div class="small text-muted">{{ $order->created_at->format('d M Y, H:i') }}</div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-sm-6">
            <div class="text-muted small">Pemesan</div>
            <div class="fw-semibold">{{ $order->user->name }}</div>
            <div class="small">{{ $order->user->email }}</div>
        </div>
        <div class="col-sm-6">
            <div class="text-muted small">Dikirim ke</div>
            <div class="fw-semibold">{{ $order->recipient_name }}</div>
            <div class="small">{{ $order->recipient_phone }}</div>
            <div class="small">{{ $order->shipping_address }}, {{ $order->city }} {{ $order->postal_code }}</div>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr><th>Menu</th><th class="text-end">Harga</th><th class="text-center">Jml</th><th class="text-end">Subtotal</th></tr>
        </thead>
        <tbody>
        @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="ms-auto" style="max-width:300px">
        <div class="d-flex justify-content-between"><span>Subtotal</span><span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
        <div class="d-flex justify-content-between"><span>Ongkos kirim</span><span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span></div>
        <hr class="my-2">
        <div class="d-flex justify-content-between fs-5 fw-bold"><span>Total</span><span>Rp {{ number_format($order->total, 0, ',', '.') }}</span></div>
    </div>

    <div class="mt-4">
        <div class="text-muted small">Status pembayaran</div>
        <div class="d-flex gap-1 flex-wrap">@include('orders._status', ['order' => $order])</div>
        @if($order->payment_status === 'paid')
            <div class="small text-muted mt-2">
                Dibayar {{ $order->paid_at?->format('d M Y, H:i') }}
                via {{ $order->payment_type ? strtoupper(str_replace('_', ' ', $order->payment_type)) : '-' }}
            </div>
        @endif
    </div>

    <div class="no-print mt-4 d-flex gap-2">
        <button class="btn btn-dark" onclick="window.print()">Cetak / Simpan PDF</button>
        <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary">Kembali</a>
    </div>
</div>
</body>
</html>
