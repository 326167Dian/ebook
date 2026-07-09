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

        .login-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #5a6b85;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin: 14px 0;
        }

        .login-divider::before,
        .login-divider::after {
            content: "";
            height: 1px;
            flex: 1;
            background: rgba(17, 45, 38, 0.18);
        }

        .btn-google {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border: 1px solid #c6d3e3;
            color: #1d3557;
            background: #fff;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-google:hover {
            color: #123157;
            border-color: #9bb5d2;
            box-shadow: 0 8px 20px rgba(31, 102, 186, 0.15);
            transform: translateY(-1px);
        }

        .google-mark {
            width: 20px;
            height: 20px;
            display: inline-block;
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

            <div class="login-divider">atau</div>

            <a href="{{ route('auth.google.redirect') }}" class="btn btn-google btn-block">
                <svg class="google-mark" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path fill="#EA4335" d="M12 10.2v3.92h5.44c-.24 1.26-.96 2.32-2.04 3.03l3.3 2.57c1.92-1.77 3.03-4.38 3.03-7.48 0-.71-.06-1.39-.2-2.04H12z" />
                    <path fill="#34A853" d="M12 22c2.7 0 4.96-.9 6.62-2.45l-3.3-2.57c-.9.6-2.04.96-3.32.96-2.55 0-4.72-1.71-5.5-4.02l-3.4 2.62C4.74 19.8 8.12 22 12 22z" />
                    <path fill="#4A90E2" d="M6.5 13.92c-.2-.6-.32-1.23-.32-1.92s.12-1.32.32-1.92L3.1 7.46C2.38 8.9 2 10.4 2 12s.38 3.1 1.1 4.54l3.4-2.62z" />
                    <path fill="#FBBC05" d="M12 6.06c1.47 0 2.78.5 3.8 1.47l2.85-2.86C16.95 3.08 14.7 2 12 2 8.12 2 4.74 4.2 3.1 7.46l3.4 2.62c.78-2.31 2.95-4.02 5.5-4.02z" />
                </svg>
                Login dengan Google
            </a>

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
