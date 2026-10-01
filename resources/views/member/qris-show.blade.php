<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="theme-color" content="#1f66ba" />
    <title>Bayar dengan QRIS</title>
    <link rel="stylesheet" href="{{ asset('Mobilekit/HTML/assets/css/style.css') }}">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(160deg, #d9efff 0%, #a8deff 50%, #7ec8ff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-card {
            width: 100%;
            max-width: 460px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(5px);
        }

        .register-head {
            background: linear-gradient(130deg, #1f66ba, #4aa4ea);
            color: #fff;
            border-radius: 18px 18px 0 0;
            padding: 22px;
        }

        #qris-image-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 8px 0 16px;
        }

        #qris-image-wrapper img {
            max-width: 280px;
            width: 100%;
            height: auto;
            border-radius: 8px;
        }

        #qris-download-link {
            margin-top: 10px;
        }

        #qris-fallback-text {
            word-break: break-all;
            font-size: 11px;
            background: #f5f5f5;
            border-radius: 8px;
            padding: 10px;
            margin: 8px 0 16px;
            text-align: left;
        }

        .btn-ebook {
            background: #1f66ba;
            color: #fff;
            border: 0;
        }
    </style>
</head>

<body>
    <div class="register-card">
        <div class="register-head">
            <h2 class="mb-1">Bayar dengan QRIS</h2>
            <p class="mb-0 opacity-75">Scan kode di bawah dengan aplikasi e-wallet atau m-banking apa pun yang mendukung QRIS.</p>
        </div>

        <div class="card-body p-3 text-center">
            @if ($qrisPayment->status === 'expired')
                <div id="qris-status-badge" class="alert alert-danger">QR sudah kedaluwarsa. Silakan kembali dan buat QR baru.</div>
            @elseif ($qrisPayment->status === 'failed')
                <div id="qris-status-badge" class="alert alert-danger">Pembayaran gagal atau dibatalkan. Silakan coba lagi.</div>
            @else
                <div id="qris-status-badge" class="alert alert-warning">Menunggu pembayaran...</div>
            @endif

            @if ($qrisPayment->status === 'pending')
                @if ($qrDataUri)
                    <div id="qris-image-wrapper">
                        <img src="{{ $qrDataUri }}" alt="QR Code QRIS">
                        <a id="qris-download-link" href="{{ $qrDataUri }}" download="qris-pembayaran.png" class="btn btn-outline-primary btn-sm">Unduh Gambar QR</a>
                        <small class="text-muted d-block mt-1">Di HP: bisa juga tekan &amp; tahan gambar QR di atas untuk simpan ke galeri.</small>
                    </div>
                @else
                    <div id="qris-fallback-text">
                        QR code gagal ditampilkan. Salin kode berikut dan buka lewat aplikasi e-wallet Anda: {{ $qrisPayment->qr_content }}
                    </div>
                @endif
            @endif

            <p class="mb-1">Total pembayaran: <strong>Rp {{ number_format($qrisPayment->amount, 0, ',', '.') }}</strong></p>
            <p class="text-muted small">
                QR ini berlaku sampai {{ $qrisPayment->expires_at->format('H:i') }}.
                Jangan tutup halaman ini, status akan terupdate otomatis begitu pembayaran diterima.
            </p>

            <a href="{{ $cancelUrl }}" class="btn btn-outline-primary btn-block mt-2">Batalkan &amp; kembali</a>
        </div>
    </div>

    <script>
        (function () {
            var isPending = @json($qrisPayment->status === 'pending');
            var statusUrl = @json(route('member.qris.status', $qrisPayment->reference_no));
            var badge = document.getElementById('qris-status-badge');

            if (!isPending) {
                return;
            }

            function setBadge(className, text) {
                badge.className = 'alert ' + className;
                badge.textContent = text;
            }

            function poll() {
                fetch(statusUrl, { headers: { 'Accept': 'application/json' } })
                    .then(function (response) { return response.json(); })
                    .then(function (data) {
                        if (data.status === 'paid') {
                            setBadge('alert-success', 'Pembayaran berhasil! Mengalihkan...');
                            setTimeout(function () { window.location.href = data.redirect; }, 1200);
                            return;
                        }

                        if (data.status === 'expired') {
                            setBadge('alert-danger', 'QR sudah kedaluwarsa. Silakan kembali dan buat QR baru.');
                            return;
                        }

                        if (data.status === 'failed') {
                            setBadge('alert-danger', 'Pembayaran gagal atau dibatalkan. Silakan coba lagi.');
                            return;
                        }

                        setTimeout(poll, 4000);
                    })
                    .catch(function () {
                        setTimeout(poll, 6000);
                    });
            }

            setTimeout(poll, 4000);
        })();
    </script>
</body>

</html>
