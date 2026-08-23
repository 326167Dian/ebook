<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="theme-color" content="#1f66ba" />
    <title>Login Reseller</title>
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
            max-width: 420px;
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
    <div class="reseller-card">
        <div class="reseller-head">
            <h2 class="mb-1">Login Reseller</h2>
            <p class="mb-0 opacity-75">Pantau member yang bergabung lewat kode referral Anda.</p>
        </div>

        <div class="card-body p-3">
            @if (session('status'))
                <div class="alert alert-success mb-3">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mb-3">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('reseller.login.submit') }}">
                @csrf

                <div class="form-group boxed">
                    <div class="input-wrapper">
                        <input type="text" class="form-control" name="username" placeholder="Username" value="{{ old('username') }}" required autofocus>
                    </div>
                </div>

                <div class="form-group boxed mt-2">
                    <div class="input-wrapper">
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-ebook btn-block mt-3">Login</button>
            </form>

            <a href="{{ route('reseller.register') }}" class="btn btn-outline-primary btn-block mt-2">Daftar Reseller Baru</a>
            <a href="{{ route('ebook.home') }}" class="btn btn-link btn-block mt-2">Kembali ke Halaman E-Book</a>
        </div>
    </div>

    <script src="{{ asset('Mobilekit/HTML/assets/js/lib/bootstrap.min.js') }}"></script>
    <script src="{{ asset('Mobilekit/HTML/assets/js/base.js') }}"></script>
</body>

</html>
