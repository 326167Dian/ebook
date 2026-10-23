<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <title>Daftar Reseller</title>
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

    </style>
</head>

<body>
    <div class="appHeader text-light">
        <div class="left"></div>
        <div class="pageTitle text-light">Daftar Reseller</div>
        <div class="right"></div>
    </div>

    <div id="appCapsule" class="pt-4 pb-4">
        <div class="section">
            <div class="editor-panel p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                    <div>
                        <h2 class="mb-1">Daftar Reseller</h2>
                        <p class="text-secondary mb-0">Reseller yang sudah disetujui/aktif.</p>
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

                @if (($approvedResellers ?? collect())->isEmpty())
                    <div class="alert alert-light border">Belum ada reseller aktif.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Username</th>
                                    <th>Telp</th>
                                    <th>Kode Promo &amp; Harga Khusus</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($approvedResellers as $reseller)
                                    <tr>
                                        <td>{{ $reseller->id_reseller }}</td>
                                        <td>{{ $reseller->nm_reseller }}</td>
                                        <td>{{ $reseller->username }}</td>
                                        <td>{{ $reseller->telp }}</td>
                                        <td>
                                            <details>
                                                <summary style="cursor: pointer;">
                                                    @if ($reseller->promo_code)
                                                        <span class="badge bg-primary">{{ $reseller->promo_code }}</span>
                                                    @else
                                                        <span class="text-muted">Belum ada kode promo</span>
                                                    @endif
                                                    @if (! is_null($reseller->custom_price_final))
                                                        <span class="badge bg-success">Rp {{ number_format($reseller->custom_price_final, 0, ',', '.') }}</span>
                                                    @endif
                                                </summary>
                                                <form method="POST" action="{{ route('admin.resellers.pricing', $reseller) }}" class="mt-2 d-flex flex-column gap-2" style="max-width: 260px;">
                                                    @csrf
                                                    <div>
                                                        <label class="form-label mb-0 small">Kode Promo (opsional)</label>
                                                        <input type="text" name="promo_code" class="form-control form-control-sm" maxlength="20" value="{{ old('promo_code', $reseller->promo_code) }}" placeholder="mis. JUMAT">
                                                    </div>
                                                    <div>
                                                        <label class="form-label mb-0 small">Harga Khusus (opsional)</label>
                                                        <input type="number" name="custom_price_final" min="0" class="form-control form-control-sm" value="{{ old('custom_price_final', $reseller->custom_price_final) }}" placeholder="kosongkan = harga promo standar">
                                                    </div>
                                                    <button type="submit" class="btn btn-sm btn-outline-primary">Simpan</button>
                                                </form>
                                            </details>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.resellers.members', $reseller) }}" class="btn btn-sm btn-primary">
                                                Detail Member
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <small class="text-muted d-block mt-1">ID di atas adalah Kode Referral yang bisa dibagikan reseller ke calon member. Kode Promo adalah alternatif berupa huruf+angka (mis. "JUMAT"). Harga Khusus menimpa harga promo standar hanya untuk reseller/kode ini.</small>

                @endif

                <div class="mt-4">
                    <a href="{{ route('admin.editor') }}" class="btn btn-primary">Kembali ke Dashboard Admin</a>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('Mobilekit/HTML/assets/js/lib/bootstrap.min.js') }}"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="{{ asset('Mobilekit/HTML/assets/js/base.js') }}"></script>
</body>

</html>
