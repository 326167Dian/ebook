<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="theme-color" content="#1f66ba" />
    <title>Verifikasi Pembayaran Member</title>
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

        .payment-card {
            width: 100%;
            max-width: 520px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(5px);
        }

        .payment-head {
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

        .btn-ebook {
            background: #1f66ba;
            color: #fff;
            border: 0;
        }

        .btn-ebook:hover {
            background: #2b7fd2;
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="payment-card">
        <div class="payment-head">
            <h2 class="mb-1">Verifikasi Pembayaran</h2>
            <p class="mb-0 opacity-75">Akun Anda sudah login, namun belum aktif untuk akses penuh e-book.</p>
        </div>

        <div class="card-body p-3">
            @if (session('status'))
                <div class="alert alert-success mb-3">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mb-3">{{ $errors->first() }}</div>
            @endif

            <div class="status-note mb-3">
                <div><strong>Member:</strong> {{ $member->name }}</div>
                <div><strong>Email:</strong> {{ $member->email }}</div>
                <div class="mt-2">Upload bukti transfer sebesar <del>Rp. 250.000</del> <strong>Rp. 99.000</strong>. Admin akan verifikasi sebelum akses penuh dibuka.</div>
            </div>

            @if (!empty($member->payment_proof_path))
                <div class="alert alert-info mb-3">
                        Bukti pembayaran sudah pernah diupload. tapi tidak sesuai, Anda bisa
                        upload ulang dengan bukti bayar yang sesuai
                    <div class="mt-2">
                        <a href="{{ asset($member->payment_proof_path) }}" target="_blank" rel="noopener">Lihat bukti yang sudah diupload</a>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('member.payment.submit') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-group boxed">
                    <div class="input-wrapper">
                        <input type="file" class="form-control" name="payment_proof" accept="image/*" required>
                    </div>
                    <small class="text-muted d-block mt-1">Format gambar: JPG, JPEG, PNG, WEBP. Maksimal 5MB.</small>
                </div>

                <button type="submit" class="btn btn-ebook btn-block mt-3">Kirim Bukti Pembayaran</button>
            </form>

            <a href="{{ route('ebook.home') }}" class="btn btn-outline-primary btn-block mt-2">Kembali ke Halaman E-Book</a>

            <form method="POST" action="{{ route('member.logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="btn btn-link btn-block">Logout</button>
            </form>
        </div>
    </div>

    <script src="{{ asset('Mobilekit/HTML/assets/js/lib/bootstrap.min.js') }}"></script>
    <script src="{{ asset('Mobilekit/HTML/assets/js/base.js') }}"></script>
</body>

</html>
