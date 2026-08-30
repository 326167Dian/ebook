<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="theme-color" content="#1f66ba" />
    <title>Dashboard Reseller</title>
    <link rel="stylesheet" href="{{ asset('Mobilekit/HTML/assets/css/style.css') }}">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(160deg, #d9efff 0%, #a8deff 50%, #7ec8ff 100%);
            padding: 20px;
        }

        .reseller-panel {
            max-width: 720px;
            margin: 0 auto;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(5px);
        }

        .reseller-head {
            background: linear-gradient(130deg, #1f66ba, #4aa4ea);
            color: #fff;
            border-radius: 18px 18px 0 0;
            padding: 22px;
        }

        .referral-note {
            border-radius: 12px;
            background: rgba(31, 102, 186, 0.09);
            border: 1px solid rgba(31, 102, 186, 0.2);
            padding: 12px;
            font-size: 14px;
        }

        .commission-proof {
            border: 1px solid #d9e2ec;
            border-radius: 6px;
            display: block;
            height: 64px;
            object-fit: cover;
            width: 88px;
        }
    </style>
</head>

<body>
    <div class="reseller-panel">
        <div class="reseller-head d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h2 class="mb-1">Dashboard Reseller</h2>
                <p class="mb-0 opacity-75">Halo, {{ $reseller->nm_reseller }}</p>
            </div>
        </div>

        <div class="card-body p-3">
            @if (session('status'))
                <div class="alert alert-success mb-3">{{ session('status') }}</div>
            @endif

            <div class="referral-note mb-3">
                Kode Referral Anda: <strong>{{ $reseller->id_reseller }}</strong><br>
                Bagikan kode ini ke calon member. Saat mereka mengisi kode ini di form upload bukti pembayaran, keanggotaan mereka akan tercatat sebagai ajakan Anda.
            </div>

            <h4 class="mb-2">Member yang Bergabung Lewat Referral Anda</h4>

            @if ($members->isEmpty())
                <div class="alert alert-light border">Belum ada member yang bergabung lewat kode referral Anda.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Member</th>
                                <th>Email</th>
                                <th>Komisi</th>
                                <th>Bukti Transfer</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($members as $index => $member)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $member->name }}</td>
                                    <td>{{ $member->email }}</td>
                                    <td>
                                        @if (!is_null($member->commission_amount))
                                            Rp {{ number_format($member->commission_amount, 0, ',', '.') }}
                                            @if ($member->commission_paid_at)
                                                <small class="text-muted d-block">{{ $member->commission_paid_at->format('d M Y') }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">Belum tersedia</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if (!empty($member->commission_proof_path))
                                            <a href="{{ asset($member->commission_proof_path) }}" target="_blank" rel="noopener" title="Klik untuk memperbesar bukti transfer">
                                                <img src="{{ asset($member->commission_proof_path) }}" alt="Bukti Transfer Komisi {{ $member->name }}" class="commission-proof">
                                            </a>
                                        @else
                                            <span class="text-muted">Belum tersedia</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <form method="POST" action="{{ route('reseller.logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="btn btn-link btn-block">Logout</button>
            </form>
        </div>
    </div>

    <script src="{{ asset('Mobilekit/HTML/assets/js/lib/bootstrap.min.js') }}"></script>
    <script src="{{ asset('Mobilekit/HTML/assets/js/base.js') }}"></script>
</body>

</html>
