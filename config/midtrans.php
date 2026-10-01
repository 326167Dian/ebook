<?php

return [
    'is_production' => filter_var(env('MIDTRANS_IS_PRODUCTION', false), FILTER_VALIDATE_BOOLEAN),
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'qr_expiry_minutes' => (int) env('MIDTRANS_QR_EXPIRY_MINUTES', 15),
];
