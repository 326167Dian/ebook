<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <title>Pengaturan Warna Front End</title>
    <link rel="stylesheet" href="{{ asset('Mobilekit/HTML/assets/css/style.css') }}">
    <style>
        :root {
            --editor-primary: #1f66ba;
            --editor-secondary: #4aa4ea;
            --editor-soft: #eef7ff;
        }

        body {
            background: radial-gradient(circle at 15% 0%, #f7fcff 0, #eaf5ff 45%, #dcebfa 100%);
            min-height: 100vh;
        }

        .appHeader {
            background: linear-gradient(120deg, var(--editor-primary), var(--editor-secondary));
        }

        .editor-panel {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 16px 32px rgba(31, 102, 186, 0.13);
            border: 1px solid rgba(31, 102, 186, 0.08);
        }

        .btn-ebook {
            background: var(--editor-primary);
            border: 0;
            color: #fff;
        }

        .btn-ebook:hover {
            color: #fff;
            background: #2b7fd2;
        }
    </style>
</head>

<body>
    <div class="appHeader text-light">
        <div class="left"></div>
        <div class="pageTitle text-light">Pengaturan Warna Front End</div>
        <div class="right"></div>
    </div>

    <div id="appCapsule" class="pt-4 pb-4">
        <div class="section">
            <div class="editor-panel p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                    <div>
                        <h2 class="mb-1">Pengaturan Warna Front End</h2>
                        <p class="text-secondary mb-0">Atur nuansa warna halaman publik eBook menggunakan color picker.</p>
                    </div>
                    <a href="{{ route('admin.editor') }}" class="btn btn-outline-secondary">Kembali ke Dashboard Admin</a>
                </div>

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.theme.update') }}">
                    @csrf

                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label">Warna Utama</label>
                            <input type="color" class="form-control form-control-color w-100" name="theme_primary" value="{{ old('theme_primary', $content->theme_primary ?? '#1e5fae') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Warna Sekunder</label>
                            <input type="color" class="form-control form-control-color w-100" name="theme_secondary" value="{{ old('theme_secondary', $content->theme_secondary ?? '#4ea3e6') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Warna Aksen</label>
                            <input type="color" class="form-control form-control-color w-100" name="theme_accent" value="{{ old('theme_accent', $content->theme_accent ?? '#9fd8ff') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Latar Awal</label>
                            <input type="color" class="form-control form-control-color w-100" name="theme_bg_start" value="{{ old('theme_bg_start', $content->theme_bg_start ?? '#f7fcff') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Latar Akhir</label>
                            <input type="color" class="form-control form-control-color w-100" name="theme_bg_end" value="{{ old('theme_bg_end', $content->theme_bg_end ?? '#dcebfa') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Warna Teks Utama</label>
                            <input type="color" class="form-control form-control-color w-100" name="theme_text" value="{{ old('theme_text', $content->theme_text ?? '#16314f') }}" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="submit" class="btn btn-ebook">Simpan Tema Warna</button>
                    </div>
                </form>

                <div class="mt-4">
                    <a href="{{ route('admin.editor') }}" class="btn btn-outline-secondary">Kembali ke Dashboard Admin</a>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('Mobilekit/HTML/assets/js/lib/bootstrap.min.js') }}"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="{{ asset('Mobilekit/HTML/assets/js/base.js') }}"></script>
</body>

</html>
