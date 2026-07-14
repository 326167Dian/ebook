<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="theme-color" content="#1f66ba" />
    <title>Registrasi Member E-Book</title>
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
            max-width: 500px;
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

        .price-note {
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
    <div class="register-card">
        <div class="register-head">
            <h2 class="mb-1">Registrasi Member</h2>
            <p class="mb-0 opacity-75">Daftar untuk akses penuh seluruh isi e-book.</p>
        </div>

        <div class="card-body p-3">
            @if (session('status'))
                <div class="alert alert-success mb-3">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mb-3">{{ $errors->first() }}</div>
            @endif

            <div class="price-note mb-3">
                Upload bukti transfer sebesar <del>Rp. 250.000 </del><strong>Rp. 99.000</strong>.<Br>
                ke Rek BCA 1391928130 a/n eneng siti wulandari<br>
                Akun akan aktif setelah diverifikasi admin.
            </div>

            <form method="POST" action="{{ route('member.register.submit') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-group boxed">
                    <div class="input-wrapper">
                        <input type="text" class="form-control" name="name" placeholder="Nama"
                            value="{{ old('name') }}" required>
                    </div>
                </div>

                <div class="form-group boxed mt-2">
                    <div class="input-wrapper">
                        <input type="email" class="form-control" name="email" placeholder="Alamat email"
                            value="{{ old('email') }}" required>
                    </div>
                </div>

                <div class="form-group boxed mt-2">
                    <div class="input-wrapper">
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                    </div>
                </div>

                <div class="form-group boxed mt-2">
                    <div class="input-wrapper">
                        <input type="password" class="form-control" name="password_confirmation"
                            placeholder="Konfirmasi password" required>
                    </div>
                </div>

                <div class="form-group boxed mt-2">
                    <div class="input-wrapper">
                        <input type="file" class="form-control" name="payment_proof" accept="image/*" required>
                    </div>
                    <small class="text-muted d-block mt-1">Format gambar: JPG, JPEG, PNG, WEBP. Maksimal 5MB.</small>
                </div>

                <button type="submit" class="btn btn-ebook btn-block mt-3">Daftar & Akses E-Book</button>
            </form>

            <a href="{{ route('member.login') }}" class="btn btn-outline-primary btn-block mt-2">Saya sudah punya
                akun</a>
            <a href="{{ route('admin.login') }}" class="btn btn-link btn-block mt-2">Masuk sebagai Admin</a>
        </div>
    </div>

    <script src="{{ asset('Mobilekit/HTML/assets/js/lib/bootstrap.min.js') }}"></script>
    <script src="{{ asset('Mobilekit/HTML/assets/js/base.js') }}"></script>
</body>

</html>
