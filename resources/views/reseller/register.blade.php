<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="theme-color" content="#1f66ba" />
    <title>Registrasi Reseller</title>
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
            max-width: 500px;
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
            <h2 class="mb-1">Registrasi Reseller</h2>
            <p class="mb-0 opacity-75">Daftar untuk mulai mengajak member baru bergabung.</p>
        </div>

        <div class="card-body p-3">
            @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('reseller.register.submit') }}">
                @csrf

                <div class="form-group boxed">
                    <div class="input-wrapper">
                        <input type="text" class="form-control" name="username" placeholder="Username" value="{{ old('username') }}" required>
                    </div>
                </div>

                <div class="form-group boxed mt-2">
                    <div class="input-wrapper">
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                    </div>
                </div>

                <div class="form-group boxed mt-2">
                    <div class="input-wrapper">
                        <input type="password" class="form-control" name="password_confirmation" placeholder="Konfirmasi password" required>
                    </div>
                </div>

                <div class="form-group boxed mt-2">
                    <div class="input-wrapper">
                        <input type="text" class="form-control" name="nm_reseller" placeholder="Nama Lengkap" value="{{ old('nm_reseller') }}" required>
                    </div>
                </div>

                <div class="form-group boxed mt-2">
                    <div class="input-wrapper">
                        <input type="text" class="form-control" name="telp" placeholder="Nomor Telepon" value="{{ old('telp') }}" required>
                    </div>
                </div>

                <div class="form-group boxed mt-2">
                    <div class="input-wrapper">
                        <textarea class="form-control" name="alamat" placeholder="Alamat (opsional)" rows="2">{{ old('alamat') }}</textarea>
                    </div>
                </div>

                <div class="form-group boxed mt-2">
                    <div class="input-wrapper">
                        <input type="text" class="form-control" name="bank" placeholder="Nama Bank" value="{{ old('bank') }}" required>
                    </div>
                </div>

                <div class="form-group boxed mt-2">
                    <div class="input-wrapper">
                        <input type="text" class="form-control" name="rekening" placeholder="Nomor Rekening" value="{{ old('rekening') }}" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-ebook btn-block mt-3">Daftar Reseller</button>
            </form>

            <a href="{{ route('reseller.login') }}" class="btn btn-outline-primary btn-block mt-2">Saya sudah punya akun</a>
        </div>
    </div>

    <script src="{{ asset('Mobilekit/HTML/assets/js/lib/bootstrap.min.js') }}"></script>
    <script src="{{ asset('Mobilekit/HTML/assets/js/base.js') }}"></script>
</body>

</html>
