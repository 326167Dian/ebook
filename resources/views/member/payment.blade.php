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
</head>

<body>
    <div class="payment-card">
        <div class="payment-head">
            <h2 class="mb-1">Verifikasi Pembayaran</h2>
            <p class="mb-0 opacity-75">Akun Anda sudah login, namun belum aktif untuk akses penuh e-book.</p>
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
                            <input type="text" inputmode="numeric" class="form-control" id="referral-qris" name="referral_code" placeholder="Kode Referral" value="{{ old('referral_code', $referralCode ?? '') }}" required>
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
                            <input type="text" inputmode="numeric" class="form-control" id="referral-midtrans" name="referral_code" placeholder="Kode Referral" value="{{ old('referral_code', $referralCode ?? '') }}" required>
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

                @if (!empty($content->payment_qris_image_path))
                    <div class="status-note mb-3">
                        Bayar sebesar <span id="price-display-manual">Rp. {{ number_format($content->payment_price_original, 0, ',', '.') }}</span><br>
                        Akun akan aktif maksimal 1 x 24 jam.
                    </div>

                    <div class="text-center mb-3">
                        <img src="{{ asset($content->payment_qris_image_path) }}" alt="QRIS Statis" style="max-width: 260px; width: 100%; height: auto; border-radius: 8px; border: 1px solid #e0e0e0;">
                        <a href="{{ asset($content->payment_qris_image_path) }}" download class="btn btn-outline-primary btn-sm d-block mt-2 mx-auto" style="max-width: 200px;">Unduh Gambar QRIS</a>
                    </div>
                @endif

                <form method="POST" action="{{ route('member.payment.submit') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_form" value="manual">

                    <div class="form-group boxed">
                        <div class="input-wrapper">
                            <input type="file" class="form-control" name="payment_proof" accept="image/*" required>
                        </div>
                        <small class="text-muted d-block mt-1">Format gambar: JPG, JPEG, PNG, WEBP. Maksimal 5MB.</small>
                    </div>

                    <div class="form-group boxed mt-2">
                        <div class="input-wrapper">
                            <input type="text" inputmode="numeric" class="form-control" id="referral-manual" name="referral_code" placeholder="Kode Referral" value="{{ old('referral_code', $referralCode ?? '') }}" required>
                        </div>
                        @if (! empty($referralCode) && ! old('referral_code'))
                            <small class="text-success d-block mt-1">Kode referral otomatis terisi dari link yang Anda buka. Anda tetap bisa mengubahnya bila perlu.</small>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-ebook btn-block mt-3">Kirim Bukti Pembayaran</button>
                </form>
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

            function wireReferralPrice(inputId, displayId) {
                var input = document.getElementById(inputId);
                var display = document.getElementById(displayId);

                if (!input || !display) {
                    return;
                }

                function updatePrice() {
                    if (input.value.trim().length > 0) {
                        display.innerHTML = '<del>' + formatRupiah(originalPrice) + '</del> <strong>' + formatRupiah(finalPrice) + '</strong>';
                    } else {
                        display.textContent = formatRupiah(originalPrice);
                    }
                }

                input.addEventListener('input', updatePrice);
                updatePrice();
            }

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
