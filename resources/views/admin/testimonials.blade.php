<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <title>Kelola Testimoni</title>
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

        .tst-item {
            border: 1px solid rgba(31, 102, 186, 0.14);
            border-radius: 14px;
            padding: 16px;
            background: #fff;
            height: 100%;
        }

        .tst-item.is-hidden {
            background: #f6f7f9;
            opacity: .85;
        }

        .tst-head {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .tst-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }

        .tst-initials {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--editor-primary);
            color: #fff;
            font-weight: 700;
        }

        .tst-stars {
            color: #f5a623;
            letter-spacing: 1px;
            font-size: 14px;
        }

        .tst-badge {
            font-size: 11px;
            font-weight: 700;
            border-radius: 999px;
            padding: 3px 10px;
        }

        .tst-badge.on {
            background: #e3f6ec;
            color: #137a43;
        }

        .tst-badge.off {
            background: #eceff3;
            color: #5b6675;
        }

        .tst-quote {
            font-size: 14px;
            color: #33475b;
            white-space: pre-line;
        }
    </style>
</head>

<body>
    <div class="appHeader text-light">
        <div class="left"></div>
        <div class="pageTitle text-light">Kelola Testimoni</div>
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
                        <h2 class="mb-1">Testimoni Pembeli</h2>
                        <p class="text-secondary mb-0">Testimoni yang berstatus <strong>Tampil</strong> akan muncul di landing page, diurutkan berdasarkan nomor urut.</p>
                    </div>
                    <a href="{{ route('admin.editor') }}" class="btn btn-outline-secondary">&laquo; Kembali ke Dashboard</a>
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

                <div class="border rounded p-3 bg-light mb-4">
                    <h5 class="mb-3">Tambah Testimoni</h5>
                    <form method="POST" action="{{ route('admin.testimonials.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Nama</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="contoh: apt. Rina Wulandari" maxlength="120" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Keterangan (opsional)</label>
                                <input type="text" class="form-control" name="role" value="{{ old('role') }}" placeholder="contoh: Pemilik Apotek, Bekasi" maxlength="150">
                            </div>
                        </div>
                        <div class="mt-2">
                            <label class="form-label">Isi Testimoni</label>
                            <textarea class="form-control" name="content" rows="4" maxlength="1000" placeholder="Tulis testimoni sesuai kata-kata pembeli." required>{{ old('content') }}</textarea>
                        </div>
                        <div class="row g-2 mt-1">
                            <div class="col-md-4">
                                <label class="form-label">Rating</label>
                                <select class="form-select form-control" name="rating">
                                    @for ($r = 5; $r >= 1; $r--)
                                        <option value="{{ $r }}" @selected((int) old('rating', 5) === $r)>{{ $r }} bintang</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Foto (opsional)</label>
                                <input type="file" class="form-control" name="photo_upload" accept="image/png,image/jpeg,image/webp">
                                <small class="text-muted d-block mt-1">JPG/PNG/WEBP, maks. 2MB. Disarankan persegi ±200x200 px. Tanpa foto akan tampil inisial nama.</small>
                            </div>
                        </div>
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" name="is_visible" value="1" id="new-visible" checked>
                            <label class="form-check-label" for="new-visible">Langsung tampilkan di landing page</label>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Tambah Testimoni</button>
                    </form>
                </div>

                <h4 class="mb-3">Daftar Testimoni ({{ $testimonials->count() }})</h4>

                @if ($testimonials->isEmpty())
                    <div class="alert alert-light border">Belum ada testimoni. Tambahkan lewat form di atas.</div>
                @else
                    <div class="row g-3">
                        @foreach ($testimonials as $t)
                            <div class="col-lg-6">
                                <div class="tst-item {{ $t->is_visible ? '' : 'is-hidden' }}">
                                    <div class="tst-head">
                                        @if (!empty($t->photo_path))
                                            <img src="{{ asset($t->photo_path) }}" alt="{{ $t->name }}" class="tst-avatar">
                                        @else
                                            <span class="tst-avatar tst-initials">{{ $t->initials }}</span>
                                        @endif
                                        <div class="flex-grow-1" style="min-width:0;">
                                            <div class="fw-semibold">{{ $t->name }}</div>
                                            <div class="small text-muted">{{ $t->role }}</div>
                                            <div class="tst-stars">{{ str_repeat('★', $t->rating) }}<span style="color:#d4dad8">{{ str_repeat('★', 5 - $t->rating) }}</span></div>
                                        </div>
                                        <span class="tst-badge {{ $t->is_visible ? 'on' : 'off' }}">{{ $t->is_visible ? 'Tampil' : 'Disembunyikan' }}</span>
                                    </div>

                                    <p class="tst-quote mb-3">&ldquo;{{ $t->content }}&rdquo;</p>

                                    <div class="d-flex flex-wrap gap-2">
                                        <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#edit-{{ $t->id }}">Edit</button>
                                        <form method="POST" action="{{ route('admin.testimonials.toggle', $t) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">{{ $t->is_visible ? 'Sembunyikan' : 'Tampilkan' }}</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.testimonials.destroy', $t) }}" onsubmit="return confirm('Hapus testimoni dari {{ addslashes($t->name) }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                        </form>
                                    </div>

                                    <div class="collapse mt-3" id="edit-{{ $t->id }}">
                                        <form method="POST" action="{{ route('admin.testimonials.update', $t) }}" enctype="multipart/form-data" class="border-top pt-3">
                                            @csrf
                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <label class="form-label small mb-1">Nama</label>
                                                    <input type="text" class="form-control form-control-sm" name="name" value="{{ $t->name }}" maxlength="120" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small mb-1">Keterangan</label>
                                                    <input type="text" class="form-control form-control-sm" name="role" value="{{ $t->role }}" maxlength="150">
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <label class="form-label small mb-1">Isi Testimoni</label>
                                                <textarea class="form-control form-control-sm" name="content" rows="4" maxlength="1000" required>{{ $t->content }}</textarea>
                                            </div>
                                            <div class="row g-2 mt-1">
                                                <div class="col-6 col-md-3">
                                                    <label class="form-label small mb-1">Rating</label>
                                                    <select class="form-select form-control form-control-sm" name="rating">
                                                        @for ($r = 5; $r >= 1; $r--)
                                                            <option value="{{ $r }}" @selected($t->rating === $r)>{{ $r }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                                <div class="col-6 col-md-3">
                                                    <label class="form-label small mb-1">No. Urut</label>
                                                    <input type="number" class="form-control form-control-sm" name="sort_order" value="{{ $t->sort_order }}" min="0">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small mb-1">Ganti Foto (opsional)</label>
                                                    <input type="file" class="form-control form-control-sm" name="photo_upload" accept="image/png,image/jpeg,image/webp">
                                                </div>
                                            </div>
                                            <div class="d-flex flex-wrap gap-3 mt-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="is_visible" value="1" id="visible-{{ $t->id }}" @checked($t->is_visible)>
                                                    <label class="form-check-label small" for="visible-{{ $t->id }}">Tampilkan</label>
                                                </div>
                                                @if (!empty($t->photo_path))
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="remove_photo" value="1" id="remove-photo-{{ $t->id }}">
                                                        <label class="form-check-label small" for="remove-photo-{{ $t->id }}">Hapus foto</label>
                                                    </div>
                                                @endif
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-primary mt-2">Simpan Perubahan</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="mt-4">
                    <a href="{{ route('admin.editor') }}" class="btn btn-outline-secondary">&laquo; Kembali ke Dashboard</a>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('Mobilekit/HTML/assets/js/lib/bootstrap.min.js') }}"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="{{ asset('Mobilekit/HTML/assets/js/base.js') }}"></script>
</body>

</html>
