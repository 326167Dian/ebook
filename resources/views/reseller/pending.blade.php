<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="theme-color" content="#1f66ba" />
    <title>Menunggu Persetujuan Reseller</title>
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

        .reseller-card {
            width: 100%;
            max-width: 460px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(5px);
        }

        .reseller-head {
            background: linear-gradient(130deg, #1f66ba, #4aa4ea);
            color: #fff;
            border-radius: 18px 18px 0 0;
            padding: 22px;
        }

        .status-note {
            border-radius: 12px;
            background: rgba(31, 102, 186, 0.09);
            border: 1px solid rgba(31, 102, 186, 0.2);
            padding: 12px;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="reseller-card">
        <div class="reseller-head">
            <h2 class="mb-1">Menunggu Persetujuan</h2>
            <p class="mb-0 opacity-75">Akun reseller Anda sedang ditinjau admin.</p>
        </div>

        <div class="card-body p-3">
            <div class="status-note mb-3">
                <div><strong>Nama:</strong> {{ $reseller->nm_reseller }}</div>
                <div><strong>Username:</strong> {{ $reseller->username }}</div>
                <div class="mt-2">Akun Anda akan aktif setelah disetujui admin. Silakan cek kembali beberapa saat lagi.</div>
            </div>

            <form method="POST" action="{{ route('reseller.logout') }}">
                @csrf
                <button type="submit" class="btn btn-link btn-block">Logout</button>
            </form>
        </div>
    </div>

    <script src="{{ asset('Mobilekit/HTML/assets/js/lib/bootstrap.min.js') }}"></script>
    <script src="{{ asset('Mobilekit/HTML/assets/js/base.js') }}"></script>
</body>

</html>
