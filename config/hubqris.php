<?php

return [
    // mysifacenter.my.id -- hub yang jadi satu-satunya partner GDC terdaftar;
    // situs ini membuat/cek QRIS lewat API cabang hub, bukan langsung ke GDC lagi
    // (GdcPayService/config/gdcpay.php lama dibiarkan apa adanya, cuma untuk
    // transaksi lama yang sudah terlanjur dibuat lewat jalur langsung).
    'base_url' => env('HUBQRIS_BASE_URL', 'https://mysifacenter.my.id'),
    'branch_code' => env('HUBQRIS_BRANCH_CODE', 'BAS'),
    'api_key' => env('HUBQRIS_API_KEY'),
    'qr_expiry_minutes' => (int) env('HUBQRIS_QR_EXPIRY_MINUTES', 15),
];
