<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    @php
        $resolveColor = function ($value, $fallback) {
            return preg_match('/^#[0-9A-Fa-f]{6}$/', (string) $value) ? $value : $fallback;
        };

        $toRgb = function ($hex) {
            $hex = ltrim($hex, '#');

            return implode(', ', [
                hexdec(substr($hex, 0, 2)),
                hexdec(substr($hex, 2, 2)),
                hexdec(substr($hex, 4, 2)),
            ]);
        };

        $themePrimary = $resolveColor($content->theme_primary ?? null, '#1e5fae');
        $themeSecondary = $resolveColor($content->theme_secondary ?? null, '#4ea3e6');
        $themeAccent = $resolveColor($content->theme_accent ?? null, '#9fd8ff');
        $themeBgStart = $resolveColor($content->theme_bg_start ?? null, '#f7fcff');
        $themeBgEnd = $resolveColor($content->theme_bg_end ?? null, '#dcebfa');
        $themeText = $resolveColor($content->theme_text ?? null, '#16314f');
        $themePrimaryRgb = $toRgb($themePrimary);
        $themeAccentRgb = $toRgb($themeAccent);
    @endphp
    <meta name="theme-color" content="{{ $themePrimary }}" />
    <title>{{ $point['title'] }} - E-Book Bisnis Apotek</title>

    <link rel="stylesheet" href="{{ asset('Mobilekit/HTML/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ebook-typography.css') }}">

    <style>
        :root {
            --ebook-primary: {{ $themePrimary }};
            --ebook-secondary: {{ $themeSecondary }};
            --ebook-accent: {{ $themeAccent }};
            --ebook-bg-start: {{ $themeBgStart }};
            --ebook-bg-end: {{ $themeBgEnd }};
            --ebook-text: {{ $themeText }};
            --ebook-primary-rgb: {{ $themePrimaryRgb }};
            --ebook-accent-rgb: {{ $themeAccentRgb }};
        }

        body {
            background: var(--ebook-app-background);
            color: var(--ebook-text);
            font-family: var(--ebook-body-font);
            min-height: 100vh;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .pageTitle,
        .point-nav-title {
            font-family: var(--ebook-title-font);
            font-weight: 800;
            color: var(--ebook-title-color);
        }

        .appHeader {
            background: linear-gradient(90deg, var(--ebook-primary), var(--ebook-secondary));
            box-shadow: 0 10px 28px rgba(var(--ebook-primary-rgb), 0.25);
            min-height: 56px;
        }

        .appHeader .pageTitle,
        .appHeader .headerButton {
            color: #fff !important;
        }

        #appCapsule {
            padding-top: 72px;
            padding-bottom: 20px;
        }

        .appHeader .pageTitle {
            font-size: 18px;
            font-weight: 700;
        }

        .appHeader .headerButton {
            width: 40px;
            height: 40px;
        }

        .point-hero {
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.94);
            color: var(--ebook-text);
            padding: 16px 18px;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(8px);
            box-shadow: 0 20px 35px rgba(16, 42, 67, 0.18);
        }

        .point-hero::after {
            content: "";
            width: 160px;
            height: 160px;
            position: absolute;
            right: -50px;
            top: -50px;
            background: radial-gradient(circle, rgba(var(--ebook-primary-rgb), 0.16) 0, rgba(var(--ebook-accent-rgb), 0) 70%);
        }

        .point-hero h2 {
            color: var(--ebook-title-color);
        }

        .point-hero .text-light {
            color: #486581 !important;
        }

        .point-content {
            border-radius: 16px;
            background: #fff;
            border: 1px solid rgba(255, 255, 255, 0.38);
            box-shadow: 0 12px 24px rgba(16, 42, 67, 0.14);
            padding: 16px 18px;
            line-height: 1.8;
            font-family: var(--ebook-body-font);
        }

        .point-content img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        .point-content figure.image {
            margin-left: 0;
            margin-right: 0;
            max-width: 100%;
        }

        .point-download-box {
            margin-top: 14px;
            padding: 14px;
            border-radius: 14px;
            background: rgba(var(--ebook-primary-rgb), 0.08);
            border: 1px solid rgba(var(--ebook-primary-rgb), 0.12);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .point-document-list {
            display: grid;
            gap: 10px;
        }

        .point-document-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid rgba(var(--ebook-primary-rgb), 0.1);
        }

        .point-document-item-name {
            font-family: var(--ebook-body-font);
            font-weight: 700;
            color: var(--ebook-title-color);
            line-height: 1.35;
        }

        .point-document-item-meta {
            font-size: 12px;
            color: #486581;
            margin-top: 2px;
            font-family: var(--ebook-body-font);
        }

        .point-download-title {
            font-family: var(--ebook-title-font);
            font-weight: 800;
            color: var(--ebook-title-color);
            margin-bottom: 4px;
        }

        .point-download-meta {
            font-size: 13px;
            color: #486581;
            font-family: var(--ebook-body-font);
        }

        .point-progress {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.02em;
            padding: 5px 10px;
            border-radius: 999px;
            background: rgba(var(--ebook-accent-rgb), 0.26);
            color: var(--ebook-title-color);
            margin-bottom: 10px;
            background: rgba(var(--ebook-primary-rgb), 0.1);
            border: 1px solid rgba(var(--ebook-primary-rgb), 0.12);
        }

        .point-progress-bar {
            width: 100%;
            height: 8px;
            border-radius: 999px;
            background: rgba(var(--ebook-primary-rgb), 0.14);
            overflow: hidden;
            margin-bottom: 12px;
        }

        .point-progress-fill {
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.9), rgba(var(--ebook-accent-rgb), 0.95));
            box-shadow: 0 4px 10px rgba(var(--ebook-accent-rgb), 0.35);
        }

        .chapter-points-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 14px;
        }

        .chapter-point-chip {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 999px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            line-height: 1.35;
            color: var(--ebook-title-color);
            background: rgba(var(--ebook-primary-rgb), 0.1);
            border: 1px solid rgba(var(--ebook-primary-rgb), 0.12);
            font-family: var(--ebook-body-font);
        }

        .chapter-point-chip.active {
            color: var(--ebook-primary);
            background: #ffffff;
            border-color: #ffffff;
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.12);
        }

        .point-nav {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .point-nav-link {
            display: block;
            border-radius: 14px;
            padding: 14px 16px;
            text-decoration: none;
            background: #fff;
            border: 1px solid rgba(255, 255, 255, 0.38);
            box-shadow: 0 12px 24px rgba(16, 42, 67, 0.12);
            color: var(--ebook-text);
            min-height: 100%;
            font-family: var(--ebook-body-font);
        }

        .point-nav-link.next {
            text-align: right;
        }

        .point-nav-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.02em;
            color: var(--ebook-title-color);
            margin-bottom: 4px;
        }

        .point-nav-title {
            display: block;
            font-size: 15px;
            font-weight: 600;
            line-height: 1.4;
        }

        @media (max-width: 576px) {
            #appCapsule {
                padding-top: 68px;
                padding-bottom: 16px;
            }

            .point-hero,
            .point-content {
                border-radius: 14px;
                padding: 14px;
            }

            .point-hero h2 {
                font-size: 1.85rem;
                line-height: 1.2;
            }

            .point-nav {
                grid-template-columns: 1fr;
            }

            .point-nav-link.next {
                text-align: left;
            }

            .point-download-box {
                flex-direction: column;
                align-items: flex-start;
            }

            .point-document-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .point-document-item .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div id="loader">
        <div class="spinner-border text-primary" role="status"></div>
    </div>

    <div class="appHeader text-light">
        <div class="left">
            <a href="{{ route('ebook.home') }}#daftar-isi" class="headerButton text-light" onclick="if (window.history.length > 1) { event.preventDefault(); window.history.back(); }">
                <ion-icon name="arrow-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle text-light">Detail Poin</div>
        <div class="right"></div>
    </div>

    <div id="appCapsule">
        <div class="section mt-2">
            <div class="point-hero">
                @if (!empty($pointNumber) && !empty($totalPoints))
                    <div class="point-progress">Poin {{ $pointNumber }} dari {{ $totalPoints }}</div>
                    <div class="point-progress-bar" aria-hidden="true">
                        <div class="point-progress-fill" style="width: {{ max(6, min(100, (int) round(($pointNumber / $totalPoints) * 100))) }}%;"></div>
                    </div>
                @endif
                <div class="text-light opacity-75 mb-1">{{ $chapter['title'] }}</div>
                <h2 class="mb-0">{{ $point['title'] }}</h2>

                @if (!empty($chapterPoints))
                    <div class="chapter-points-nav">
                        @foreach ($chapterPoints as $chapterPoint)
                            <a href="{{ route('ebook.point', ['slug' => $chapterPoint['slug']]) }}" class="chapter-point-chip {{ ($chapterPoint['slug'] ?? '') === ($point['slug'] ?? '') ? 'active' : '' }}">
                                {{ $chapterPoint['title'] }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="section mt-3">
            <div class="point-content">
                @if (!empty($point['content']))
                    {!! $point['content'] !!}
                @else
                    <p class="mb-0">Konten untuk poin ini belum diisi. Silakan login admin untuk menambahkan isi halaman.</p>
                @endif

                @php
                    $documents = collect($point['documents'] ?? []);
                    if ($documents->isEmpty() && !empty($point['document_path'])) {
                        $documents = collect([[ 'path' => $point['document_path'], 'name' => $point['document_name'] ?? '' ]]);
                    }
                @endphp

                @if ($documents->isNotEmpty())
                    <div class="point-download-box">
                        <div>
                            <div class="point-download-title">Dokumen Lampiran</div>
                            <div class="point-download-meta">
                                {{ $documents->count() }} dokumen siap diunduh
                            </div>
                        </div>
                        <div class="point-document-list w-100">
                            @foreach ($documents as $document)
                                @if (!empty($document['path']))
                                    <div class="point-document-item">
                                        <div>
                                            <div class="point-document-item-name">{{ $document['name'] ?: basename($document['path']) }}</div>
                                            <div class="point-document-item-meta">{{ basename($document['path']) }}</div>
                                        </div>
                                        <a href="{{ asset($document['path']) }}" class="btn btn-sm btn-outline-primary" download>
                                            Download
                                        </a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @if ($previousPoint || $nextPoint)
            <div class="section mt-3">
                <div class="point-nav">
                    <div>
                        @if ($previousPoint)
                            <a href="{{ route('ebook.point', ['slug' => $previousPoint['point']['slug']]) }}" class="point-nav-link previous">
                                <span class="point-nav-label">Poin Sebelumnya</span>
                                <span class="point-nav-title">{{ $previousPoint['point']['title'] }}</span>
                            </a>
                        @endif
                    </div>
                    <div>
                        @if ($nextPoint)
                            <a href="{{ route('ebook.point', ['slug' => $nextPoint['point']['slug']]) }}" class="point-nav-link next">
                                <span class="point-nav-label">Poin Berikutnya</span>
                                <span class="point-nav-title">{{ $nextPoint['point']['title'] }}</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script src="{{ asset('Mobilekit/HTML/assets/js/lib/bootstrap.min.js') }}"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="{{ asset('Mobilekit/HTML/assets/js/base.js') }}"></script>
</body>

</html>
