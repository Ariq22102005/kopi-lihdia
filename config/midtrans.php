<?php

return [
    'server_key'     => env('MIDTRANS_SERVER_KEY'),
    'client_key'     => env('MIDTRANS_CLIENT_KEY'),
    'is_production'  => (bool) env('MIDTRANS_IS_PRODUCTION', false),

    // Batas waktu pembayaran sejak token dibuat (menit)
    'expiry_minutes' => (int) env('MIDTRANS_EXPIRY_MINUTES', 60),
];
