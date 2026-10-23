<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <title>Cek New Anggota / Reseller</title>
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

        .member-approval-card {
            border: 1px solid rgba(31, 102, 186, 0.15);
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 10px;
            background: #fff;
        }

        .member-approval-proof {
            max-width: 220px;
            width: 100%;
            height: auto;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            margin-top: 8px;
        }

        .member-approval-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="appHeader text-light">
        <div class="left"></div>
        <div class="pageTitle text-light">Cek New Anggota / Reseller</div>
        <div class="right"></div>
    </div>

    <div id="appCapsule" class="pt-4 pb-4">
        <div class="section">
            <div class="editor-panel p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                    <div>
                        <h2 class="mb-1">Cek New Anggota / Reseller</h2>
                        <p class="text-secondary mb-0">Approval pendaftar member baru, calon pembeli, dan reseller - terpisah dari halaman edit isi e-book.</p>
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

                <div class="mb-4">
                    <h3 class="mb-2">Moderasi Member</h3>
                    <p class="text-secondary mb-2">Approve pendaftar setelah nominal di bukti transfer sesuai tagihan masing-masing (Rp. {{ number_format($content->payment_price_final, 0, ',', '.') }} dengan kode referral/promo, Rp. {{ number_format($content->payment_price_original, 0, ',', '.') }} tanpa kode). Pendaftar yang belum upload bukti transfer tidak ditampilkan di sini.</p>

                    @if (($pendingMembers ?? collect())->isEmpty())
                        <div class="alert alert-light border">Tidak ada pendaftar yang menunggu verifikasi.</div>
                    @else
                        @foreach ($pendingMembers as $member)
                            <div class="member-approval-card">
                                <div><strong>{{ $member->name }}</strong></div>
                                <div class="small text-muted">{{ $member->email }}</div>
                                @if (!empty($member->phone))
                                    <div class="small"><a href="{{ \App\Models\Member::whatsappLink($member->phone) }}" target="_blank" rel="noopener">WA: {{ $member->phone }}</a></div>
                                @endif
                                <div class="small text-muted">Daftar: {{ optional($member->created_at)->format('d M Y H:i') }}</div>
                                <div class="small">
                                    Tagihan:
                                    <strong>Rp. {{ number_format($member->id_reseller ? $content->payment_price_final : $content->payment_price_original, 0, ',', '.') }}</strong>
                                    <span class="text-muted">({{ $member->id_reseller ? 'kode referral #' . $member->id_reseller : 'tanpa kode, harga normal' }})</span>
                                </div>

                                @if (!empty($member->payment_proof_path))
                                    <a href="{{ asset($member->payment_proof_path) }}" target="_blank" rel="noopener">
                                        <img src="{{ asset($member->payment_proof_path) }}" alt="Bukti Transfer {{ $member->name }}" class="member-approval-proof">
                                    </a>
                                @endif

                                <div class="member-approval-actions">
                                    <form method="POST" action="{{ route('admin.members.approve', $member) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.members.reject', $member) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Tolak</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="mb-4">
                    <h3 class="mb-2">Calon Pembeli (belum upload bukti)</h3>
                    <p class="text-secondary mb-2">Sudah mengisi nama, email, dan WhatsApp, tapi belum mengirim bukti bayar (30 hari terakhir). Hubungi lewat WhatsApp untuk follow up. Jika pembeli sudah bayar dan mengirim bukti lewat WhatsApp, klik Approve.</p>

                    @if (($leadMembers ?? collect())->isEmpty())
                        <div class="alert alert-light border">Belum ada calon pembeli.</div>
                    @else
                        @foreach ($leadMembers as $lead)
                            @php
                                $leadPrice = \App\Models\EbookContent::priceFor($lead->id_reseller);
                                $leadWa = \App\Models\Member::whatsappLink(
                                    $lead->phone,
                                    "Halo {$lead->name}, terima kasih sudah mendaftar E-Book Bisnis Apotek Itu Asik.\n"
                                    . 'Apakah ada kendala saat pembayaran Rp' . number_format($leadPrice, 0, ',', '.') . '? Kami siap membantu.'
                                );
                            @endphp
                            <div class="member-approval-card">
                                <div><strong>{{ $lead->name }}</strong></div>
                                <div class="small text-muted">{{ $lead->email }}</div>
                                <div class="small text-muted">Daftar: {{ optional($lead->created_at)->format('d M Y H:i') }} &middot; Tagihan Rp. {{ number_format($leadPrice, 0, ',', '.') }}</div>

                                <div class="member-approval-actions">
                                    @if ($leadWa)
                                        <a href="{{ $leadWa }}" target="_blank" rel="noopener" class="btn btn-sm btn-success">Follow up WA ({{ $lead->phone }})</a>
                                    @else
                                        <span class="small text-muted">Tidak ada nomor WA (daftar lewat Google)</span>
                                    @endif
                                    <form method="POST" action="{{ route('admin.members.approve', $lead) }}" onsubmit="return confirm('Pembayaran calon pembeli ini sudah diterima?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Approve (sudah bayar via WA)</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="mb-4">
                    <h3 class="mb-2">Approval Reseller</h3>
                    <p class="text-secondary mb-2">Setujui pendaftar reseller agar bisa login dan melihat member yang bergabung lewat kode referalnya.</p>

                    @if (($pendingResellers ?? collect())->isEmpty())
                        <div class="alert alert-light border">Tidak ada pendaftar reseller yang menunggu persetujuan.</div>
                    @else
                        @foreach ($pendingResellers as $reseller)
                            <div class="member-approval-card">
                                <div><strong>{{ $reseller->nm_reseller }}</strong> (username: {{ $reseller->username }})</div>
                                <div class="small text-muted">Telp: {{ $reseller->telp }}</div>
                                <div class="small text-muted">Bank: {{ $reseller->bank }} - {{ $reseller->rekening }}</div>
                                <div class="small text-muted">Daftar: {{ optional($reseller->created_at)->format('d M Y H:i') }}</div>

                                <div class="member-approval-actions">
                                    <form method="POST" action="{{ route('admin.resellers.approve', $reseller) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.resellers.reject', $reseller) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Tolak</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

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
