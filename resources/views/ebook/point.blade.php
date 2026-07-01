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
            background: radial-gradient(circle at 20% 0%, var(--ebook-bg-start) 0, #eaf5ff 45%, var(--ebook-bg-end) 100%);
            color: var(--ebook-text);
        }

        .appHeader {
            background: linear-gradient(90deg, var(--ebook-primary), var(--ebook-secondary));
            box-shadow: 0 10px 28px rgba(var(--ebook-primary-rgb), 0.25);
        }

        #appCapsule {
            padding-top: 80px;
            padding-bottom: 24px;
        }

        .point-hero {
            border-radius: 16px;
            background: linear-gradient(165deg, var(--ebook-primary), var(--ebook-secondary));
            color: #fff;
            padding: 18px;
            position: relative;
            overflow: hidden;
        }

        .point-hero::after {
            content: "";
            width: 160px;
            height: 160px;
            position: absolute;
            right: -50px;
            top: -50px;
            background: radial-gradient(circle, rgba(var(--ebook-accent-rgb), 0.5) 0, rgba(var(--ebook-accent-rgb), 0) 70%);
        }

        .point-content {
            border-radius: 16px;
            background: #fff;
            border: 1px solid rgba(var(--ebook-primary-rgb), 0.12);
            box-shadow: 0 12px 24px rgba(var(--ebook-primary-rgb), 0.1);
            padding: 18px;
            line-height: 1.8;
        }
    </style>
</head>

<body>
    <div id="loader">
        <div class="spinner-border text-primary" role="status"></div>
    </div>

    <div class="appHeader text-light">
        <div class="left">
            <a href="{{ route('ebook.home') }}#daftar-isi" class="headerButton text-light">
                <ion-icon name="arrow-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle text-light">Detail Poin</div>
        <div class="right">
            <a href="{{ route('admin.login') }}" class="headerButton text-light">
                <ion-icon name="create-outline"></ion-icon>
            </a>
        </div>
    </div>

    <div id="appCapsule">
        <div class="section mt-2">
            <div class="point-hero">
                <div class="text-light opacity-75 mb-1">{{ $chapter['title'] }}</div>
                <h2 class="mb-0">{{ $point['title'] }}</h2>
            </div>
        </div>

        <div class="section mt-3">
            <div class="point-content">
                @if (!empty($point['content']))
                    {!! $point['content'] !!}
                @else
                    <p class="mb-0">Konten untuk poin ini belum diisi. Silakan login admin untuk menambahkan isi halaman.</p>
                @endif
            </div>
        </div>
    </div>

    <script src="{{ asset('Mobilekit/HTML/assets/js/lib/bootstrap.min.js') }}"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="{{ asset('Mobilekit/HTML/assets/js/base.js') }}"></script>
</body>

</html>
