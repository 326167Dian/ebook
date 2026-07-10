<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="theme-color" content="#112d26" />
    <title>Login Admin E-Book</title>
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

        .login-card {
            width: 100%;
            max-width: 420px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(5px);
        }

        .login-head {
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
    <div class="login-card">
        <div class="login-head">
            <h2 class="mb-1">Admin E-Book</h2>
            <p class="mb-0 opacity-75">Login untuk mengelola tampilan frontend secara dinamis.</p>
        </div>

        <div class="card-body p-3">
            @if (session('error'))
                <div class="alert alert-warning mb-3">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf
                <div class="form-group boxed">
                    <div class="input-wrapper">
                        <input type="email" class="form-control" name="email" placeholder="Email admin" value="{{ old('email') }}" required>
                    </div>
                </div>

                <div class="form-group boxed mt-2">
                    <div class="input-wrapper">
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                    </div>
                </div>

                <div class="form-check mt-2 mb-3">
                    <input class="form-check-input" type="checkbox" value="1" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Ingat saya</label>
                </div>

                <button type="submit" class="btn btn-ebook btn-block">Masuk</button>
            </form>

            @if ($canSetup)
                <a href="{{ route('admin.setup') }}" class="btn btn-outline-secondary btn-block mt-2">Buat Admin Pertama</a>
            @endif

            <a href="{{ route('ebook.home') }}" class="btn btn-link btn-block mt-2">Kembali ke Halaman E-Book</a>
        </div>
    </div>

    <script src="{{ asset('Mobilekit/HTML/assets/js/lib/bootstrap.min.js') }}"></script>
    <script src="{{ asset('Mobilekit/HTML/assets/js/base.js') }}"></script>
</body>

</html>
