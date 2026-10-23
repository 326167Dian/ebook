<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="theme-color" content="#1f66ba" />
    <title>Verifikasi Pembayaran Member</title>
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

        .payment-card {
            width: 100%;
            max-width: 520px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(5px);
        }

        .payment-head {
            background: linear-gradient(130deg, #1f66ba, #4aa4ea);
            color: #fff;
            border-radius: 18px 18px 0 0;
            padding: 22px;
        }

        .status-note {
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
    {{-- InitiateCheckout dikirim sekali dari langkah 1 (lewat session flash di partial
         meta-pixel), bukan setiap kali halaman ini dibuka/di-refresh. --}}
    <style>
        .step-label {
            display: inline-block;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            background: rgba(255, 255, 255, .2);
            border-radius: 999px;
            padding: 3px 10px;
            margin-bottom: 8px;
        }

        .pay-steps {
            margin: 0 0 12px;
            padding-left: 0;
            list-style: none;
            counter-reset: paystep;
        }

        .pay-steps li {
            position: relative;
            padding: 6px 0 6px 34px;
            font-size: 14px;
            line-height: 1.5;
            counter-increment: paystep;
        }

        .pay-steps li::before {
            content: counter(paystep);
            position: absolute;
            left: 0;
            top: 6px;
            width: 24px;
            height: 24px;
            border-radius: 999px;
            background: #1f66ba;
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .inapp-tip {
            display: none;
            border-radius: 12px;
            background: #fff8e1;
            border: 1px solid #f0c36d;
            padding: 10px 12px;
            font-size: 13px;
            margin-bottom: 12px;
        }

        .btn-wa {
            background: #25d366;
            color: #fff !important;
            border: 0;
            font-weight: 700;
        }

        body {
            padding-bottom: 80px;
        }
    </style>
</head>

<body>
    <div class="payment-card">
        <div class="payment-head">
            <span class="step-label">Langkah 2 dari 2 &middot; Pembayaran</span>
            <h2 class="mb-1">Selesaikan Pembayaran</h2>
            <p class="mb-0 opacity-75">Bayar dengan QRIS, lalu upload bukti pembayarannya di halaman ini.</p>
        </div>

        <div class="card-body p-3">
            @if (session('status'))
                <div class="alert alert-success mb-3">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mb-3">{{ $errors->first() }}</div>
            @endif

            <div class="status-note mb-3">
                <div><strong>Member:</strong> {{ $member->name }}</div>
                <div><strong>Email:</strong> {{ $member->email }}</div>
            </div>

            @if (!empty($member->payment_proof_path))
                @if (!empty($member->payment_rejected_at))
                    <div class="alert alert-danger mb-3">
                        Bukti pembayaran sebelumnya ditolak admin karena tidak sesuai. Silakan
                        upload ulang dengan bukti bayar yang sesuai.
                        <div class="mt-2">
                            <a href="{{ asset($member->payment_proof_path) }}" target="_blank" rel="noopener">Lihat bukti yang sudah diupload</a>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info mb-3">
                        Bukti pembayaran sudah dikirim dan sedang menunggu verifikasi admin.
                        <div class="mt-2">
                            <a href="{{ asset($member->payment_proof_path) }}" target="_blank" rel="noopener">Lihat bukti yang sudah diupload</a>
                        </div>
                    </div>
                @endif
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
                <div class="status-note mb-3">
                    Bayar otomatis sebesar <span id="price-display-qris">Rp. {{ number_format($content->payment_price_original, 0, ',', '.') }}</span> via QRIS
                    (GoPay, OVO, DANA, ShopeePay, m-Banking, dll).<br>
                    Akun langsung aktif otomatis begitu pembayaran terverifikasi, tanpa perlu upload bukti transfer.
                </div>

                <form method="POST" action="{{ route('member.payment.qris.create') }}">
                    @csrf
                    <input type="hidden" name="_form" value="qris">

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
                <div class="status-note mb-3">
                    Bayar otomatis sebesar <span id="price-display-midtrans">Rp. {{ number_format($content->payment_price_original, 0, ',', '.') }}</span> via QRIS Midtrans
                    (GoPay, OVO, DANA, ShopeePay, m-Banking, dll).<br>
                    Akun langsung aktif otomatis begitu pembayaran terverifikasi, tanpa perlu upload bukti transfer.
                </div>

                <form method="POST" action="{{ route('member.payment.midtrans.create') }}">
                    @csrf
                    <input type="hidden" name="_form" value="midtrans">

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
                <div class="status-note mb-3">
                    Upload bukti transfer sebesar <span id="price-display-manual">Rp. {{ number_format($content->payment_price_original, 0, ',', '.') }}</span>.<br>
                    Ke No Rekening berikut ini<br>
                    <strong>{{ $content->payment_bank_name }} {{ $content->payment_bank_account_number }} a/n {{ $content->payment_bank_account_holder }}</strong><br>
                    {{ $content->payment_note }}
                </div>
                @endif

                @php
                    $needsPayment = empty($member->payment_proof_path) || !empty($member->payment_rejected_at);
                @endphp

                @if (!empty($content->payment_qris_image_path) && $needsPayment)
                    <div class="status-note mb-3">
                        Total bayar: <strong id="price-display-manual">Rp. {{ number_format($price ?? $content->payment_price_original, 0, ',', '.') }}</strong><br>
                        Akses e-book dibuka setelah admin memverifikasi bukti pembayaran Anda.
                    </div>

                    <ol class="pay-steps">
                        <li><strong>Simpan gambar QRIS</strong> di bawah: tekan tombol <em>Simpan QRIS</em>, atau tekan lama gambarnya lalu pilih <em>Simpan/Download gambar</em>.</li>
                        <li><strong>Buka m-Banking atau e-wallet</strong> (GoPay, OVO, DANA, ShopeePay, dll.), pilih <em>Bayar/Scan QRIS</em>, ketuk ikon <em>galeri</em>, lalu pilih gambar QRIS tadi dan masukkan nominal sesuai total bayar.</li>
                        <li><strong>Screenshot bukti berhasil</strong>, kembali ke halaman ini, lalu upload di bawah.</li>
                    </ol>

                    <div class="inapp-tip" id="inapp-tip">
                        Anda membuka halaman ini dari aplikasi Facebook/Instagram. Jika tombol <em>Simpan QRIS</em> tidak berfungsi,
                        <strong>tekan lama gambar QRIS</strong> lalu pilih simpan, atau
                        @if (!empty($waHelpLink))
                            <a href="{{ $waHelpLink }}" target="_blank" rel="noopener">minta QRIS dikirim lewat WhatsApp</a>.
                        @else
                            scan dari HP/laptop lain.
                        @endif
                        Data Anda sudah tersimpan, jadi aman bila halaman ini termuat ulang.
                    </div>

                    <div class="text-center mb-3">
                        <img id="qris-image" src="{{ asset($content->payment_qris_image_path) }}" alt="QRIS Statis" style="max-width: 260px; width: 100%; height: auto; border-radius: 8px; border: 1px solid #e0e0e0;">
                        <a id="qris-download" href="{{ asset($content->payment_qris_image_path) }}" download="QRIS-Ebook-Bisnis-Apotek.jpg" class="btn btn-outline-primary btn-sm d-block mt-2 mx-auto" style="max-width: 200px;">Simpan QRIS</a>
                        <small class="text-muted d-block mt-1">Dari laptop/HP lain? Cukup scan QR di layar ini.</small>
                    </div>
                @endif

                <form method="POST" action="{{ route('member.payment.submit') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_form" value="manual">

                    <div class="form-group boxed">
                        <label class="form-label">{{ $needsPayment ? 'Upload Bukti Pembayaran' : 'Salah upload? Kirim ulang bukti pembayaran' }}</label>
                        <div class="input-wrapper">
                            <input type="file" class="form-control" name="payment_proof" accept="image/*" required>
                        </div>
                        <small class="text-muted d-block mt-1">Screenshot bukti berhasil bayar (JPG, PNG, WEBP). Maksimal 5MB.</small>
                    </div>

                    <div class="form-group boxed mt-2">
                        <div class="input-wrapper">
                            <input type="text" autocapitalize="characters" maxlength="20" class="form-control" id="referral-manual" name="referral_code" placeholder="Kode Referral / Promo (opsional)" value="{{ old('referral_code', $referralCode ?? '') }}">
                        </div>
                        @if (! empty($referralCode) && ! old('referral_code'))
                            <small class="text-success d-block mt-1">Kode referral otomatis terisi dari link yang Anda buka. Anda tetap bisa mengubahnya bila perlu.</small>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-ebook btn-block mt-3">Kirim Bukti Pembayaran</button>
                </form>

                @if (!empty($waHelpLink))
                    <a href="{{ $waHelpLink }}" target="_blank" rel="noopener" class="btn btn-wa btn-block mt-2">Kesulitan bayar/upload? Chat WhatsApp</a>
                @endif
            </div>

            <a href="{{ route('ebook.home') }}" class="btn btn-outline-primary btn-block mt-2">Kembali ke Halaman E-Book</a>

            <form method="POST" action="{{ route('member.logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="btn btn-link btn-block">Logout</button>
            </form>
        </div>
    </div>

    <script src="{{ asset('Mobilekit/HTML/assets/js/lib/bootstrap.min.js') }}"></script>
    <script src="{{ asset('Mobilekit/HTML/assets/js/base.js') }}"></script>
    <script>
        (function () {
            // Browser bawaan Facebook/Instagram sering mengabaikan atribut download.
            var ua = navigator.userAgent || '';
            var inApp = /FBAN|FBAV|FB_IAB|FBIOS|Instagram/i.test(ua);
            var tip = document.getElementById('inapp-tip');

            if (inApp && tip) {
                tip.style.display = 'block';
            }

            // Simpan QRIS sebagai file (lebih andal daripada link download biasa).
            var btn = document.getElementById('qris-download');

            if (!btn || !window.fetch || !window.URL || !URL.createObjectURL) {
                return;
            }

            btn.addEventListener('click', function (e) {
                e.preventDefault();

                fetch(btn.href, { credentials: 'same-origin' })
                    .then(function (res) {
                        if (!res.ok) { throw new Error('gagal'); }
                        return res.blob();
                    })
                    .then(function (blob) {
                        var url = URL.createObjectURL(blob);
                        var a = document.createElement('a');
                        a.href = url;
                        a.download = btn.getAttribute('download') || 'QRIS.jpg';
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        setTimeout(function () { URL.revokeObjectURL(url); }, 4000);
                    })
                    .catch(function () {
                        window.open(btn.href, '_blank');
                    })
                    .finally(function () {
                        if (inApp && tip) {
                            tip.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    });
            });
        })();

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

                function render(code, valid, price) {
                    if (input.value.trim().toUpperCase() !== code) {
                        return;
                    }
                    if (valid) {
                        state = 'valid';
                        display.innerHTML = '<del>' + formatRupiah(originalPrice) + '</del> <strong>' + formatRupiah(price || finalPrice) + '</strong>';
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
                        render(code, referralCache[code].valid, referralCache[code].price);
                        return;
                    }

                    state = 'checking';
                    showOriginal();
                    note.className = 'd-block mt-1 text-muted';
                    note.textContent = 'Memeriksa kode...';

                    fetch(referralCheckUrl + '?code=' + encodeURIComponent(code), { headers: { 'Accept': 'application/json' } })
                        .then(function (res) { return res.ok ? res.json() : { valid: false }; })
                        .then(function (data) {
                            referralCache[code] = { valid: !!data.valid, price: data.price || null };
                            render(code, !!data.valid, data.price || null);
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
