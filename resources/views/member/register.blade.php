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

        .pay-tab-btn {
            border: 1px solid #1f66ba;
            background: #fff;
            color: #1f66ba;
            border-radius: 10px;
            padding: 8px 10px;
            font-weight: 600;
            font-size: 14px;
        }

        .pay-tab-btn.active {
            background: #1f66ba;
            color: #fff;
        }
    </style>
    @include('partials.meta-pixel')
    <script>
        if (window.fbq) {
            fbq('track', 'InitiateCheckout', {
                value: {{ (int) ($content->payment_price_original ?? 0) }},
                currency: 'IDR',
                content_name: 'E-Book Bisnis Apotek Itu Asik'
            });
        }
    </script>
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

            @if (false)
            {{-- Tab switcher disembunyikan sementara: Midtrans juga dihide agar domain yang sedang ditinjau GDC hanya menampilkan QRIS Statis, menghindari kesalahpahaman bisnis. Kode disimpan untuk diaktifkan lagi nanti. --}}
            <div class="d-flex mb-3" style="gap: 8px;">
                <button type="button" id="pay-tab-btn-midtrans" class="pay-tab-btn flex-fill active" onclick="switchPayTab('midtrans')">Bayar Otomatis QRIS</button>
                <button type="button" id="pay-tab-btn-manual" class="pay-tab-btn flex-fill" onclick="switchPayTab('manual')">QRIS Statis</button>
            </div>
            @endif

            @if (false)
            <div id="pay-tab-qris" style="display: none;">
                <div class="price-note mb-3">
                    Bayar otomatis sebesar <span id="price-display-qris">Rp. {{ number_format($content->payment_price_original, 0, ',', '.') }}</span> via QRIS
                    (GoPay, OVO, DANA, ShopeePay, m-Banking, dll).<br>
                    Akun langsung aktif otomatis begitu pembayaran terverifikasi, tanpa perlu upload bukti transfer.
                </div>

                <form method="POST" action="{{ route('member.register.qris.create') }}">
                    @csrf
                    <input type="hidden" name="_form" value="qris">

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
                            <input type="text" autocapitalize="characters" maxlength="20" class="form-control" id="referral-qris" name="referral_code" placeholder="Kode Referral / Promo (opsional)" value="{{ old('referral_code', $referralCode ?? '') }}">
                        </div>
                        @if (! empty($referralCode) && ! old('referral_code'))
                            <small class="text-success d-block mt-1">Kode referral otomatis terisi dari link yang Anda buka. Anda tetap bisa mengubahnya bila perlu.</small>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-ebook btn-block mt-3">Lanjut Bayar dengan QRIS</button>
                </form>
            </div>
            @endif

            @if (false)
            <div id="pay-tab-midtrans">
                <div class="price-note mb-3">
                    Bayar otomatis sebesar <span id="price-display-midtrans">Rp. {{ number_format($content->payment_price_original, 0, ',', '.') }}</span> via QRIS Midtrans
                    (GoPay, OVO, DANA, ShopeePay, m-Banking, dll).<br>
                    Akun langsung aktif otomatis begitu pembayaran terverifikasi, tanpa perlu upload bukti transfer.
                </div>

                <form method="POST" action="{{ route('member.register.midtrans.create') }}">
                    @csrf
                    <input type="hidden" name="_form" value="midtrans">

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
                            <input type="text" autocapitalize="characters" maxlength="20" class="form-control" id="referral-midtrans" name="referral_code" placeholder="Kode Referral / Promo (opsional)" value="{{ old('referral_code', $referralCode ?? '') }}">
                        </div>
                        @if (! empty($referralCode) && ! old('referral_code'))
                            <small class="text-success d-block mt-1">Kode referral otomatis terisi dari link yang Anda buka. Anda tetap bisa mengubahnya bila perlu.</small>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-ebook btn-block mt-3">Lanjut Bayar dengan Midtrans</button>
                </form>
            </div>
            @endif

            <div id="pay-tab-manual">
                @if (false)
                {{-- Keterangan transfer bank disembunyikan, fokus ke QRIS statis saja (kode disimpan untuk diaktifkan lagi nanti). --}}
                <div class="price-note mb-3">
                    Upload bukti transfer sebesar <span id="price-display-manual">Rp. {{ number_format($content->payment_price_original, 0, ',', '.') }}</span>.<br>
                    Ke No Rekening berikut ini<br>
                    <strong>{{ $content->payment_bank_name }} {{ $content->payment_bank_account_number }} a/n {{ $content->payment_bank_account_holder }}</strong><br>
                    Akun akan aktif setelah diverifikasi admin.
                </div>
                @endif

                @if (!empty($content->payment_qris_image_path))
                    <div class="price-note mb-3">
                        Bayar sebesar <span id="price-display-manual">Rp. {{ number_format($content->payment_price_original, 0, ',', '.') }}</span><br>
                        Akun akan aktif maksimal 1 x 24 jam setelah upload bukti transfer.
                    </div>

                    <div class="text-center mb-3">
                        <img src="{{ asset($content->payment_qris_image_path) }}" alt="QRIS Statis" style="max-width: 260px; width: 100%; height: auto; border-radius: 8px; border: 1px solid #e0e0e0;">
                        <a href="{{ asset($content->payment_qris_image_path) }}" download class="btn btn-outline-primary btn-sm d-block mt-2 mx-auto" style="max-width: 200px;">Unduh Gambar QRIS</a>
                    </div>
                @endif

                <form method="POST" action="{{ route('member.register.submit') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_form" value="manual">

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
                        <label class="form-label">Upload Bukti Transfer</label>
                        <div class="input-wrapper">
                            <input type="file" class="form-control" name="payment_proof" accept="image/*" required>
                        </div>
                        <small class="text-muted d-block mt-1">Format gambar: JPG, JPEG, PNG, WEBP. Maksimal 5MB.</small>
                    </div>

                    <div class="form-group boxed mt-2">
                        <div class="input-wrapper">
                            <input type="text" autocapitalize="characters" maxlength="20" class="form-control" id="referral-manual" name="referral_code" placeholder="Kode Referral / Promo (opsional)" value="{{ old('referral_code', $referralCode ?? '') }}">
                        </div>
                        @if (! empty($referralCode) && ! old('referral_code'))
                            <small class="text-success d-block mt-1">Kode referral otomatis terisi dari link yang Anda buka. Anda tetap bisa mengubahnya bila perlu.</small>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-ebook btn-block mt-3">Daftar & Akses E-Book</button>
                </form>
            </div>

            <a href="{{ route('member.login') }}" class="btn btn-outline-primary btn-block mt-2">Saya sudah punya
                akun</a>
            {{-- <a href="{{ route('admin.login') }}" class="btn btn-link btn-block mt-2">Masuk sebagai Admin</a> --}}
        </div>
    </div>

    <script src="{{ asset('Mobilekit/HTML/assets/js/lib/bootstrap.min.js') }}"></script>
    <script src="{{ asset('Mobilekit/HTML/assets/js/base.js') }}"></script>
    <script>
        function switchPayTab(tab) {
            ['midtrans', 'manual'].forEach(function (name) {
                var panel = document.getElementById('pay-tab-' + name);
                var btn = document.getElementById('pay-tab-btn-' + name);

                if (panel) {
                    panel.style.display = tab === name ? '' : 'none';
                }
                if (btn) {
                    btn.classList.toggle('active', tab === name);
                }
            });
        }

        (function () {
            var originalPrice = {{ (int) $content->payment_price_original }};
            var finalPrice = {{ (int) $content->payment_price_final }};

            function formatRupiah(amount) {
                return 'Rp. ' + amount.toString().replace(/\B(?=(\d{3})+(?!\d)$)/g, '.');
            }

            var referralCheckUrl = @json(route('referral.check'));
            var referralCache = {};

            // Harga diskon hanya ditampilkan setelah kode dicek ke server dan valid.
            function wireReferralPrice(inputId, displayId) {
                var input = document.getElementById(inputId);
                var display = document.getElementById(displayId);

                if (!input || !display) {
                    return;
                }

                var note = document.createElement('small');
                note.className = 'd-block mt-1';
                var wrapper = input.closest('.input-wrapper') || input;
                wrapper.parentNode.insertBefore(note, wrapper.nextSibling);

                var timer = null;
                var state = 'empty';

                function showOriginal() {
                    display.textContent = formatRupiah(originalPrice);
                }

                function render(code, valid) {
                    if (input.value.trim().toUpperCase() !== code) {
                        return;
                    }
                    if (valid) {
                        state = 'valid';
                        display.innerHTML = '<del>' + formatRupiah(originalPrice) + '</del> <strong>' + formatRupiah(finalPrice) + '</strong>';
                        note.className = 'd-block mt-1 text-success';
                        note.textContent = '\u2713 Kode valid, harga promo berlaku.';
                    } else {
                        state = 'invalid';
                        showOriginal();
                        note.className = 'd-block mt-1 text-danger';
                        note.textContent = 'Kode tidak ditemukan. Periksa kembali, atau kosongkan kolom ini untuk membayar harga normal.';
                    }
                }

                function check() {
                    var code = input.value.trim().toUpperCase();

                    if (code.length === 0) {
                        state = 'empty';
                        showOriginal();
                        note.textContent = '';
                        return;
                    }

                    if (Object.prototype.hasOwnProperty.call(referralCache, code)) {
                        render(code, referralCache[code]);
                        return;
                    }

                    state = 'checking';
                    showOriginal();
                    note.className = 'd-block mt-1 text-muted';
                    note.textContent = 'Memeriksa kode...';

                    fetch(referralCheckUrl + '?code=' + encodeURIComponent(code), { headers: { 'Accept': 'application/json' } })
                        .then(function (res) { return res.ok ? res.json() : { valid: false }; })
                        .then(function (data) {
                            referralCache[code] = !!data.valid;
                            render(code, !!data.valid);
                        })
                        .catch(function () {
                            // Gagal cek (jaringan): jangan blokir, server tetap memvalidasi saat submit.
                            state = 'unknown';
                            note.textContent = '';
                        });
                }

                input.addEventListener('input', function () {
                    clearTimeout(timer);
                    timer = setTimeout(check, 500);
                });

                if (input.form) {
                    input.form.addEventListener('submit', function (e) {
                        if (state === 'invalid') {
                            e.preventDefault();
                            input.focus();
                        }
                    });
                }

                check();
            }

            wireReferralPrice('referral-qris', 'price-display-qris');
            wireReferralPrice('referral-midtrans', 'price-display-midtrans');
            wireReferralPrice('referral-manual', 'price-display-manual');

            var submittedForm = @json(old('_form'));
            if (submittedForm === 'manual') {
                switchPayTab(submittedForm);
            }
        })();
    </script>
</body>

</html>
