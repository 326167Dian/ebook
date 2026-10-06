<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="theme-color" content="#1f66ba" />
    <title>Bayar dengan Midtrans QRIS</title>
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

        #qris-img {
            max-width: 260px;
            width: 100%;
            height: auto;
            border-radius: 8px;
        }

        #qris-download-link {
            margin-top: 10px;
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
            <h2 class="mb-1">Bayar dengan QRIS (Midtrans)</h2>
            <p class="mb-0 opacity-75">Scan kode di bawah dengan aplikasi e-wallet atau m-banking apa pun yang mendukung QRIS.</p>
        </div>

        <div class="card-body p-3 text-center">
            @if ($payment->status === 'expired')
                <div id="status-badge" class="alert alert-danger">QR sudah kedaluwarsa. Silakan kembali dan buat QR baru.</div>
            @elseif ($payment->status === 'failed')
                <div id="status-badge" class="alert alert-danger">Pembayaran gagal atau dibatalkan. Silakan coba lagi.</div>
            @else
                <div id="status-badge" class="alert alert-warning">Menunggu pembayaran...</div>
            @endif

            @if ($payment->status === 'pending' && $payment->qr_image_url)
                <div id="qris-image-wrapper">
                    <img id="qris-img" src="{{ $payment->qr_image_url }}" alt="QR Code QRIS Midtrans">
                    <a id="qris-download-link" href="{{ $payment->qr_image_url }}" download="qris-midtrans.png" class="btn btn-outline-primary btn-sm">Unduh Gambar QR</a>
                    <small class="text-muted d-block mt-1">Di HP: bisa juga tekan &amp; tahan gambar QR di atas untuk simpan ke galeri.</small>
                </div>

                @if (! config('midtrans.is_production'))
                    <div class="text-start mt-2 mb-3" style="font-size: 12px;">
                        <label class="form-label mb-1">Mode sandbox - URL gambar QR (untuk simulator Midtrans):</label>
                        <div class="input-group">
                            <input type="text" id="qris-url-copy" class="form-control form-control-sm" value="{{ $payment->qr_image_url }}" readonly onclick="this.select()">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="copyQrisUrl()">Salin</button>
                        </div>
                        <a href="https://simulator.sandbox.midtrans.com/v2/qris/index" target="_blank" rel="noopener" class="d-block mt-1">Buka Simulator QRIS Midtrans &rarr;</a>
                    </div>
                @endif
            @endif

            <p class="mb-1">Total pembayaran: <strong>Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong></p>
            <p class="text-muted small">
                QR ini berlaku sampai {{ $payment->expires_at->format('H:i') }}.
                Jangan tutup halaman ini, status akan terupdate otomatis begitu pembayaran diterima.
            </p>

            <a href="{{ $cancelUrl }}" class="btn btn-outline-primary btn-block mt-2">Batalkan &amp; kembali</a>
        </div>
    </div>

    <script>
        function copyQrisUrl() {
            var input = document.getElementById('qris-url-copy');
            input.select();
            input.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(input.value).catch(function () {
                document.execCommand('copy');
            });
        }

        (function () {
            var isPending = @json($payment->status === 'pending');
            var statusUrl = @json(route('member.midtrans.status', $payment->order_id));
            var badge = document.getElementById('status-badge');

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
