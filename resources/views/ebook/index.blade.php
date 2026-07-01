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
    <title>E-Book Bisnis Apotek</title>

    <link rel="stylesheet" href="{{ asset('Mobilekit/HTML/assets/css/style.css') }}">

    <style>
        :root {
            --ebook-primary: {{ $themePrimary }};
            --ebook-secondary: {{ $themeSecondary }};
            --ebook-accent: {{ $themeAccent }};
            --ebook-bg-start: {{ $themeBgStart }};
            --ebook-bg-end: {{ $themeBgEnd }};
            --ebook-card: #ffffff;
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
            padding-top: 78px;
            padding-bottom: 24px;
        }

        .hero-box {
            background: linear-gradient(165deg, var(--ebook-primary) 0%, var(--ebook-secondary) 100%);
            color: #fff;
            border-radius: 20px;
            padding: 20px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 35px rgba(var(--ebook-primary-rgb), 0.22);
        }

        .hero-box::after {
            content: "";
            width: 180px;
            height: 180px;
            position: absolute;
            right: -65px;
            top: -65px;
            background: radial-gradient(circle, rgba(var(--ebook-accent-rgb), 0.55) 0, rgba(var(--ebook-accent-rgb), 0) 70%);
        }

        .cover-frame {
            border-radius: 14px;
            overflow: hidden;
            border: 3px solid rgba(255, 255, 255, 0.2);
            background: var(--ebook-primary);
            min-height: 320px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cover-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            max-height: 500px;
        }

        .toc-card {
            border-radius: 16px;
            background: var(--ebook-card);
            box-shadow: 0 14px 30px rgba(var(--ebook-primary-rgb), 0.12);
            border: 1px solid rgba(var(--ebook-primary-rgb), 0.08);
            animation: riseIn 0.55s ease both;
        }

        .toc-card + .toc-card {
            margin-top: 12px;
        }

        .toc-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
            font-weight: 700;
            color: var(--ebook-primary);
            cursor: pointer;
        }

        .toc-no {
            width: 28px;
            height: 28px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-size: 12px;
            color: #fff;
            background: linear-gradient(145deg, var(--ebook-primary), var(--ebook-secondary));
        }

        .toc-body {
            padding: 0 16px 14px 16px;
        }

        .toc-list {
            margin: 0;
            padding-left: 18px;
        }

        .toc-list li {
            margin: 4px 0;
        }

        .hero-badge {
            background: rgba(var(--ebook-accent-rgb), 0.28);
            color: #eaf7ff;
            border: 1px solid rgba(234, 247, 255, 0.45);
            font-size: 12px;
            font-weight: 600;
            border-radius: 999px;
            padding: 5px 10px;
            display: inline-block;
            margin-bottom: 10px;
        }

        @keyframes riseIn {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
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
            <a href="#" class="headerButton text-light" onclick="window.scrollTo({ top: 0, behavior: 'smooth' }); return false;">
                <ion-icon name="arrow-up-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle text-light">{{ $content->hero_title }}</div>
        <div class="right">
            <a href="{{ route('admin.login') }}" class="headerButton text-light" title="Login Admin">
                <ion-icon name="person-circle-outline"></ion-icon>
            </a>
            <a href="#daftar-isi" class="headerButton text-light">
                <ion-icon name="list-outline"></ion-icon>
            </a>
        </div>
    </div>

    <div id="appCapsule" class="pt-4">
        <div class="section mt-2">
            <div class="hero-box">
                <span class="hero-badge">{{ $content->badge }}</span>
                <h2 class="mb-2">{{ $content->hero_title }}</h2>
                <p class="mb-0 text-light opacity-75">{{ $content->hero_description }}</p>
            </div>
        </div>

        <div class="section mt-3">
            <div class="card" style="border-radius:16px; overflow:hidden; box-shadow:0 16px 32px rgba(0,0,0,.08);">
                <div class="cover-frame">
                    @php
                        $coverImage = $content->cover_image;

                        if (blank($coverImage) && !empty($covers)) {
                            $coverImage = $covers[0];
                        }
                    @endphp

                    @if (!blank($coverImage))
                        <img src="{{ asset($coverImage) }}" alt="Cover E-Book">
                    @else
                        <div class="text-center px-3">
                            <ion-icon name="book-outline" style="font-size:58px; color:var(--ebook-accent);"></ion-icon>
                            <p class="mt-2 mb-0 text-muted">Cover belum tersedia di folder public/coverebook</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="section mt-4" id="daftar-isi">
            <div class="section-title">DAFTAR ISI</div>
            @php
                $chapters = $content->chapters ?? [];
            @endphp

            @foreach ($chapters as $index => $chapter)
                <div class="toc-card" style="animation-delay: {{ $index * 0.05 }}s;">
                    <div class="toc-head" data-bs-toggle="collapse" data-bs-target="#chapter-{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}">
                        <div>
                            <span class="toc-no">{{ $index + 1 }}</span>
                            {{ $chapter['title'] }}
                        </div>
                        <ion-icon name="chevron-down-outline"></ion-icon>
                    </div>
                    <div id="chapter-{{ $index }}" class="collapse {{ $index === 0 ? 'show' : '' }}">
                        <div class="toc-body">
                            <ul class="toc-list">
                                @foreach ($chapter['items'] as $item)
                                    <li>
                                        <a href="{{ route('ebook.point', ['slug' => $item['slug']]) }}" style="color: var(--ebook-primary); font-weight: 600;">
                                            {{ $item['title'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="section mt-4">
            <div class="card" style="border-radius: 16px; border:1px solid rgba(var(--ebook-primary-rgb),.12); box-shadow:0 14px 24px rgba(var(--ebook-primary-rgb),.10);">
                <div class="card-body">
                    <h3 class="mb-2" style="color:var(--ebook-primary);">Catatan Pengantar</h3>
                    <p class="mb-0 text-secondary">{{ $content->intro_note }}</p>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('Mobilekit/HTML/assets/js/lib/bootstrap.min.js') }}"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="{{ asset('Mobilekit/HTML/assets/js/base.js') }}"></script>
</body>

</html>
