<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="theme-color" content="#1f66ba" />
    <title>Reset Password Member</title>
    <link rel="stylesheet" href="{{ asset('Mobilekit/HTML/assets/css/style.css') }}">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(155deg, #d9efff 0%, #9fd8ff 45%, #5fb6ff 120%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .reset-card {
            width: 100%;
            max-width: 430px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(5px);
        }

        .reset-head {
            background: linear-gradient(130deg, #1f66ba, #4aa4ea);
            color: #fff;
            border-radius: 18px 18px 0 0;
            padding: 22px;
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
    <div class="reset-card">
        <div class="reset-head">
            <h2 class="mb-1">Reset Password</h2>
            <p class="mb-0 opacity-75">Masukkan password baru untuk akun member Anda.</p>
        </div>

        <div class="card-body p-3">
            @if ($errors->any())
                <div class="alert alert-danger mb-3">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('member.password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group boxed">
                    <div class="input-wrapper">
                        <input type="email" class="form-control" name="email" placeholder="Alamat email" value="{{ old('email', $email) }}" required>
                    </div>
                </div>

                <div class="form-group boxed mt-2">
                    <div class="input-wrapper">
                        <input type="password" class="form-control" name="password" placeholder="Password baru" required>
                    </div>
                </div>

                <div class="form-group boxed mt-2">
                    <div class="input-wrapper">
                        <input type="password" class="form-control" name="password_confirmation" placeholder="Konfirmasi password baru" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-ebook btn-block mt-3">Simpan Password Baru</button>
            </form>

            <a href="{{ route('member.login') }}" class="btn btn-outline-primary btn-block mt-2">Kembali ke Login</a>
        </div>
    </div>

    <script src="{{ asset('Mobilekit/HTML/assets/js/lib/bootstrap.min.js') }}"></script>
    <script src="{{ asset('Mobilekit/HTML/assets/js/base.js') }}"></script>
</body>

</html>
