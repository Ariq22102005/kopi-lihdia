@php
    $payment = [
        'pending' => ['Menunggu Pembayaran', 'warning'],
        'paid'    => ['Sudah Dibayar', 'success'],
        'expired' => ['Kedaluwarsa', 'secondary'],
        'failed'  => ['Gagal', 'danger'],
    ];
    $process = [
        'pending'    => ['Menunggu', 'secondary'],
        'processing' => ['Diproses', 'primary'],
        'shipped'    => ['Dikirim', 'info'],
        'completed'  => ['Selesai', 'success'],
        'cancelled'  => ['Dibatalkan', 'danger'],
    ];
    [$payLabel, $payColor] = $payment[$order->payment_status] ?? [$order->payment_status, 'secondary'];
    [$procLabel, $procColor] = $process[$order->order_status] ?? [$order->order_status, 'secondary'];
@endphp
<span class="badge text-bg-{{ $payColor }}">Bayar: {{ $payLabel }}</span>
<span class="badge text-bg-{{ $procColor }}">Pesanan: {{ $procLabel }}</span>
