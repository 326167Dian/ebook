<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <title>Setup Admin Pertama</title>
    <link rel="stylesheet" href="{{ asset('Mobilekit/HTML/assets/css/style.css') }}">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(155deg, #d9efff 0%, #a7dcff 40%, #64bbff 120%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .setup-card {
            width: 100%;
            max-width: 540px;
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body>
    <div class="setup-card">
        <div class="card-body p-3 p-md-4">
            <h2 class="mb-1">Setup Admin Pertama</h2>
            <p class="text-secondary mb-3">Form ini hanya tersedia saat akun admin belum ada.</p>

            @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.setup.store') }}">
                @csrf

                <div class="form-group boxed">
                    <div class="input-wrapper">
                        <input type="text" class="form-control" name="name" placeholder="Nama admin" value="{{ old('name') }}" required>
                    </div>
                </div>

                <div class="form-group boxed mt-2">
                    <div class="input-wrapper">
                        <input type="email" class="form-control" name="email" placeholder="Email admin" value="{{ old('email') }}" required>
                    </div>
                </div>

                <div class="form-group boxed mt-2">
                    <div class="input-wrapper">
                        <input type="password" class="form-control" name="password" placeholder="Password minimal 8 karakter" required>
                    </div>
                </div>

                <div class="form-group boxed mt-2">
                    <div class="input-wrapper">
                        <input type="password" class="form-control" name="password_confirmation" placeholder="Ulangi password" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block mt-3">Simpan dan Masuk Admin</button>
            </form>

            <a href="{{ route('admin.login') }}" class="btn btn-link btn-block mt-2">Kembali ke Login</a>
        </div>
    </div>

    <script src="{{ asset('Mobilekit/HTML/assets/js/lib/bootstrap.min.js') }}"></script>
</body>

</html>
