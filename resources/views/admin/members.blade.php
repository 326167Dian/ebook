<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <title>Daftar Member</title>
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
        <div class="pageTitle text-light">Daftar Member</div>
        <div class="right"></div>
    </div>

    <div id="appCapsule" class="pt-4 pb-4">
        <div class="section">
            <div class="editor-panel p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                    <div>
                        <h2 class="mb-1">Daftar Member</h2>
                        <p class="text-secondary mb-0">Member yang sudah aktif/terverifikasi.</p>
                    </div>
                    <a href="{{ route('admin.editor') }}" class="btn btn-outline-secondary">Kembali ke Dashboard Admin</a>
                </div>

                @if (($approvedMembers ?? collect())->isEmpty())
                    <div class="alert alert-light border">Belum ada member aktif.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Aktif Sejak</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($approvedMembers as $member)
                                    <tr>
                                        <td>{{ $member->name }}</td>
                                        <td>{{ $member->email }}</td>
                                        <td>{{ optional($member->updated_at)->format('d M Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
