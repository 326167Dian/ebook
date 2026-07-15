<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Logo Apotek Pendukung</title>
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
    <div id="loader">
        <div class="spinner-border text-primary" role="status"></div>
    </div>

    <div class="appHeader text-light">
        <div class="left"></div>
        <div class="pageTitle text-light">Logo Apotek Pendukung</div>
        <div class="right">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="headerButton text-light" style="border:0; background:transparent;">
                    <ion-icon name="log-out-outline"></ion-icon>
                </button>
            </form>
        </div>
    </div>

    <div id="appCapsule" class="pt-4 pb-4">
        <div class="section">
            <div class="editor-panel p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                    <div>
                        <h2 class="mb-1">Logo Apotek Pendukung</h2>
                        <p class="text-secondary mb-0">Kelola logo apotek yang tampil di bagian bawah dashboard, setelah section Tentang Penulis.</p>
                    </div>
                    <a href="{{ route('admin.editor') }}" class="btn btn-outline-secondary">&laquo; Kembali ke Editor</a>
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

                @if (($pharmacyLogos ?? collect())->isNotEmpty())
                    <div class="row g-3 mb-3">
                        @foreach ($pharmacyLogos as $logo)
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <img src="{{ asset($logo->logo_path) }}" alt="{{ $logo->name }}" style="width:72px;height:56px;object-fit:contain;background:#f5f9ff;border-radius:8px;padding:4px;">
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold">{{ $logo->name }}</div>
                                            @if (!empty($logo->website_url))
                                                <a href="{{ $logo->website_url }}" target="_blank" rel="noopener" class="small text-muted d-inline-block text-truncate" style="max-width:220px;">{{ $logo->website_url }}</a>
                                            @endif
                                        </div>
                                    </div>

                                    <form method="POST" action="{{ route('admin.pharmacy-logos.update', $logo) }}" enctype="multipart/form-data" class="mb-2">
                                        @csrf
                                        <div class="mb-2">
                                            <label class="form-label small mb-1">Nama Apotek</label>
                                            <input type="text" class="form-control form-control-sm" name="name" value="{{ $logo->name }}" required>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small mb-1">Website (opsional)</label>
                                            <input type="url" class="form-control form-control-sm" name="website_url" value="{{ $logo->website_url }}" placeholder="https://...">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small mb-1">Ganti Logo (opsional)</label>
                                            <input type="file" class="form-control form-control-sm" name="logo_upload" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-primary">Simpan Perubahan</button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.pharmacy-logos.destroy', $logo) }}" onsubmit="return confirm('Hapus logo {{ $logo->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-light border">Belum ada logo apotek. Tambahkan lewat form di bawah.</div>
                @endif

                <div class="border rounded p-3 bg-light">
                    <h5 class="mb-3">Tambah Logo Apotek</h5>
                    <form method="POST" action="{{ route('admin.pharmacy-logos.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Nama Apotek</label>
                                <input type="text" class="form-control" name="name" placeholder="contoh: Apotek Sehat" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Website (opsional)</label>
                                <input type="url" class="form-control" name="website_url" placeholder="https://...">
                            </div>
                        </div>
                        <div class="mt-2">
                            <label class="form-label">Upload Logo</label>
                            <input type="file" class="form-control" name="logo_upload" accept="image/png,image/jpeg,image/webp,image/svg+xml" required>
                            <small class="text-muted d-block mt-1">Format: JPG, PNG, WEBP, SVG. Maksimal 5MB.</small>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Tambah Logo</button>
                    </form>
                </div>

                <div class="mt-4">
                    <a href="{{ route('admin.editor') }}" class="btn btn-outline-secondary">&laquo; Kembali ke Editor</a>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('Mobilekit/HTML/assets/js/lib/bootstrap.min.js') }}"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="{{ asset('Mobilekit/HTML/assets/js/base.js') }}"></script>
</body>

</html>
