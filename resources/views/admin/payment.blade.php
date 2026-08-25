<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <title>Pengaturan Pembayaran</title>
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
        <div class="pageTitle text-light">Pengaturan Pembayaran</div>
        <div class="right"></div>
    </div>

    <div id="appCapsule" class="pt-4 pb-4">
        <div class="section">
            <div class="editor-panel p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                    <div>
                        <h2 class="mb-1">Pengaturan Pembayaran</h2>
                        <p class="text-secondary mb-0">Atur harga dan keterangan yang tampil di halaman registrasi &amp; verifikasi pembayaran member.</p>
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

                <form method="POST" action="{{ route('admin.payment.update') }}">
                    @csrf

                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Harga Awal (Rp)</label>
                            <input type="number" min="0" class="form-control" name="payment_price_original" value="{{ old('payment_price_original', $content->payment_price_original) }}" required>
                            <small class="text-muted d-block mt-1">Ditampilkan tercoret sebagai harga sebelum diskon.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Harga Setelah Diskon (Rp)</label>
                            <input type="number" min="0" class="form-control" name="payment_price_final" value="{{ old('payment_price_final', $content->payment_price_final) }}" required>
                            <small class="text-muted d-block mt-1">Harga yang harus ditransfer member.</small>
                        </div>
                    </div>

                    <div class="row g-2 mt-1">
                        <div class="col-md-4">
                            <label class="form-label">Nama Bank</label>
                            <input type="text" class="form-control" name="payment_bank_name" maxlength="80" value="{{ old('payment_bank_name', $content->payment_bank_name) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nomor Rekening</label>
                            <input type="text" class="form-control" name="payment_bank_account_number" maxlength="50" value="{{ old('payment_bank_account_number', $content->payment_bank_account_number) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Atas Nama</label>
                            <input type="text" class="form-control" name="payment_bank_account_holder" maxlength="120" value="{{ old('payment_bank_account_holder', $content->payment_bank_account_holder) }}" required>
                        </div>
                        <small class="text-muted d-block mt-1">Ditampilkan sebagai nomor rekening tujuan transfer di halaman registrasi &amp; verifikasi pembayaran.</small>
                    </div>

                    <div class="form-group mt-3">
                        <label class="form-label">Keterangan Pembayaran</label>
                        <input type="text" class="form-control" name="payment_note" maxlength="255" value="{{ old('payment_note', $content->payment_note) }}" required>
                        <small class="text-muted d-block mt-1">Kalimat yang tampil setelah harga di halaman verifikasi pembayaran.</small>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="submit" class="btn btn-ebook">Simpan Pengaturan Pembayaran</button>
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
