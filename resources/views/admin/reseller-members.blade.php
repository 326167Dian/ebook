<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Detail Member Reseller</title>
    <link rel="stylesheet" href="{{ asset('Mobilekit/HTML/assets/css/style.css') }}">
    <style>
        :root {
            --editor-primary: #1f66ba;
            --editor-secondary: #4aa4ea;
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
            border: 1px solid rgba(31, 102, 186, 0.08);
            border-radius: 18px;
            box-shadow: 0 16px 32px rgba(31, 102, 186, 0.13);
        }

        .commission-proof {
            border: 1px solid #d9e2ec;
            border-radius: 6px;
            cursor: zoom-in;
            height: 72px;
            object-fit: cover;
            width: 100px;
        }

        .commission-form {
            min-width: 245px;
        }
    </style>
</head>

<body>
    <div class="appHeader text-light">
        <div class="left"></div>
        <div class="pageTitle text-light">Detail Member Reseller</div>
        <div class="right"></div>
    </div>

    <div id="appCapsule" class="pt-4 pb-4">
        <div class="section">
            <div class="editor-panel p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                    <div>
                        <h2 class="mb-1">Member Rekrutan {{ $reseller->nm_reseller }}</h2>
                        <p class="text-secondary mb-0">Username: {{ $reseller->username }} | Kode referral: {{ $reseller->id_reseller }}</p>
                    </div>
                    <a href="{{ route('admin.resellers.index') }}" class="btn btn-outline-secondary">Kembali ke Daftar Reseller</a>
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

                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Komisi dan Bukti Transfer</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($members as $member)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $member->name }}</td>
                                    <td>{{ $member->email }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.resellers.commission-proof', [$reseller, $member]) }}" enctype="multipart/form-data" class="commission-form">
                                            @csrf
                                            <label for="commission-amount-{{ $member->id }}" class="form-label small mb-1">Nominal komisi</label>
                                            <input id="commission-amount-{{ $member->id }}" type="number" name="commission_amount" class="form-control form-control-sm" min="0" step="1" value="{{ old('commission_amount', $member->commission_amount) }}" placeholder="Nominal komisi" required>
                                            <label for="commission-proof-{{ $member->id }}" class="form-label small mb-1 mt-2">Bukti transfer</label>
                                            <input id="commission-proof-{{ $member->id }}" type="file" name="commission_proof" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp" required>
                                            <small class="text-muted d-block mt-1">JPG, JPEG, PNG, WEBP. Maksimal 5 MB.</small>
                                            <button type="submit" class="btn btn-sm btn-primary mt-2">Simpan Komisi</button>
                                        </form>

                                        @if (!empty($member->commission_proof_path))
                                            <a href="{{ asset($member->commission_proof_path) }}" target="_blank" rel="noopener" class="d-inline-block mt-3">
                                                <img src="{{ asset($member->commission_proof_path) }}" alt="Bukti Transfer Komisi {{ $member->name }}" class="commission-proof">
                                            </a>
                                            <small class="text-muted d-block">Dibayar: {{ optional($member->commission_paid_at)->format('d M Y H:i') }}</small>
                                        @else
                                            <span class="text-muted d-block mt-3">Belum dibayar</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada member yang direkrut reseller ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <script src="{{ asset('Mobilekit/HTML/assets/js/lib/bootstrap.min.js') }}"></script>
    <script src="{{ asset('Mobilekit/HTML/assets/js/base.js') }}"></script>
</body>

</html>
