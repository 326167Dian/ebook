<?php

return [
    'base_url' => env('GDCPAY_BASE_URL', 'https://neo-sandbox-api.gdc.co.id'),
    'client_key' => env('GDCPAY_CLIENT_KEY'),
    'client_secret' => env('GDCPAY_CLIENT_SECRET'),
    'private_key_path' => env('GDCPAY_PRIVATE_KEY_PATH') ?: storage_path('app/private/gdcpay/private.pem'),
    'partner_id' => env('GDCPAY_PARTNER_ID'),
    'merchant_id' => env('GDCPAY_MERCHANT_ID'),
    'account_number' => env('GDCPAY_ACCOUNT_NUMBER'),
    'channel_id' => env('GDCPAY_CHANNEL_ID', ''),
    'qr_expiry_minutes' => (int) env('GDCPAY_QR_EXPIRY_MINUTES', 15),
];
