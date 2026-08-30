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
                                            <a href="{{ route('admin.resellers.members', $reseller) }}" class="btn btn-sm btn-primary">
                                                Detail Member
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <small class="text-muted d-block mt-1">ID di atas adalah Kode Referral yang bisa dibagikan reseller ke calon member.</small>

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
