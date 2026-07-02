<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    @php
        $resolveColor = function ($value, $fallback) {
            return preg_match('/^#[0-9A-Fa-f]{6}$/', (string) $value) ? $value : $fallback;
        };

        $authorName = trim((string) ($content->author_name ?? ''));
        $authorName = $authorName !== '' ? $authorName : 'Admin Penulis';

        $authorInitials = collect(preg_split('/\s+/', $authorName, -1, PREG_SPLIT_NO_EMPTY))
            ->take(2)
            ->map(fn ($part) => function_exists('mb_substr') ? mb_substr($part, 0, 1) : substr($part, 0, 1))
            ->implode('');
        $authorInitials = strtoupper($authorInitials !== '' ? $authorInitials : 'AP');

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
    <link rel="stylesheet" href="{{ asset('css/ebook-typography.css') }}">

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
        .section-title,
        .toc-head,
        .pageTitle {
            font-family: var(--ebook-title-font);
            font-weight: 800;
            color: var(--ebook-title-color);
        }

        .appHeader {
            background: linear-gradient(90deg, var(--ebook-primary), var(--ebook-secondary));
            box-shadow: 0 10px 28px rgba(var(--ebook-primary-rgb), 0.25);
        }

        .appHeader .pageTitle,
        .appHeader .headerButton {
            color: #fff !important;
        }

        .appHeader .pageTitle {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .appHeader .page-logo {
            display: block;
            max-height: 32px;
            width: auto;
            object-fit: contain;
        }

        #appCapsule {
            padding-top: 78px;
            padding-bottom: 24px;
        }

        .hero-box {
            background: rgba(255, 255, 255, 0.94);
            color: var(--ebook-text);
            border-radius: 20px;
            padding: 20px;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(8px);
            box-shadow: 0 20px 35px rgba(16, 42, 67, 0.18);
        }

        .hero-box::after {
            content: "";
            width: 180px;
            height: 180px;
            position: absolute;
            right: -65px;
            top: -65px;
            background: radial-gradient(circle, rgba(var(--ebook-primary-rgb), 0.16) 0, rgba(var(--ebook-accent-rgb), 0) 70%);
        }

        .hero-box h2 {
            color: var(--ebook-title-color);
        }

        .hero-box .text-light {
            color: #486581 !important;
        }

        .cover-frame {
            border-radius: 14px;
            overflow: hidden;
            border: 3px solid rgba(255, 255, 255, 0.35);
            background: rgba(255, 255, 255, 0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px;
        }

        .cover-frame img {
            width: 100%;
            height: auto;
            max-height: 78vh;
            object-fit: contain;
            border-radius: 10px;
        }

        @media (max-width: 576px) {
            .cover-frame img {
                max-height: 62vh;
            }
        }

        .toc-card {
            border-radius: 16px;
            background: var(--ebook-card);
            box-shadow: 0 14px 30px rgba(16, 42, 67, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.38);
            animation: riseIn 0.55s ease both;
        }

        .toc-card + .toc-card {
            margin-top: 12px;
        }

        .rich-content {
            line-height: 1.8;
            font-family: var(--ebook-body-font);
            color: #5b7083;
        }

        .rich-content > :last-child {
            margin-bottom: 0;
        }

        .rich-content p {
            margin: 0 0 0.95em;
            text-align: justify;
            text-indent: 1.6em;
        }

        .rich-content p:first-child {
            margin-top: 0;
        }

        .rich-content ul,
        .rich-content ol {
            margin: 0 0 1em;
            padding-left: 1.4em;
        }

        .rich-content li {
            text-align: justify;
            margin-bottom: 0.35em;
        }

        .rich-content strong,
        .rich-content b {
            color: #173453;
            font-weight: 800;
        }

        .author-card {
            position: relative;
            overflow: hidden;
            border-radius: 22px;
            border: 1px solid rgba(var(--ebook-primary-rgb), 0.14);
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.98), rgba(244, 250, 255, 0.95));
            box-shadow: 0 18px 34px rgba(var(--ebook-primary-rgb), 0.12);
        }

        .author-card::before {
            content: "";
            position: absolute;
            inset: 0 auto auto 0;
            width: 100%;
            height: 8px;
            background: linear-gradient(90deg, var(--ebook-primary), var(--ebook-secondary), var(--ebook-accent));
        }

        .author-card::after {
            content: "";
            position: absolute;
            right: -60px;
            top: -40px;
            width: 180px;
            height: 180px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(var(--ebook-primary-rgb), 0.13) 0, rgba(var(--ebook-primary-rgb), 0) 70%);
            pointer-events: none;
        }

        .author-card-body {
            position: relative;
            z-index: 1;
            padding: 24px 20px 20px;
        }

        .author-grid {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 14px;
        }

        .author-photo-frame {
            border-radius: 12px;
            overflow: hidden;
            background: linear-gradient(145deg, rgba(var(--ebook-primary-rgb), 0.12), rgba(var(--ebook-primary-rgb), 0.03));
            border: 1px solid rgba(var(--ebook-primary-rgb), 0.14);
            box-shadow: 0 12px 28px rgba(var(--ebook-primary-rgb), 0.12);
            aspect-ratio: 2 / 3;
            width: clamp(82px, 24vw, 96px);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .author-photo-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .author-photo-placeholder {
            width: 72px;
            height: 72px;
            border-radius: 999px;
            background: rgba(var(--ebook-primary-rgb), 0.10);
            color: var(--ebook-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            font-weight: 800;
            font-family: var(--ebook-title-font);
        }

        .author-copy {
            min-width: 0;
            width: 100%;
            padding-top: 2px;
        }

        .author-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            padding: 6px 12px;
            background: rgba(var(--ebook-primary-rgb), 0.08);
            color: var(--ebook-primary);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .author-eyebrow::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: var(--ebook-secondary);
            box-shadow: 0 0 0 5px rgba(var(--ebook-primary-rgb), 0.08);
        }

        .author-title {
            margin: 10px 0 4px;
            color: var(--ebook-primary);
            line-height: 1.15;
        }

        .author-name {
            margin-bottom: 8px;
            font-family: var(--ebook-title-font);
            font-size: 0.98rem;
            font-weight: 700;
            color: #35516d;
        }

        .author-lead {
            display: none;
        }

        @media (max-width: 576px) {
            .author-photo-frame {
                width: clamp(82px, 30vw, 96px);
            }

            .author-card-body {
                padding: 22px 16px 18px;
            }

            .rich-content p {
                text-indent: 1.3em;
            }
        }

        .rich-content img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        .rich-content figure.image {
            margin-left: 0;
            margin-right: 0;
            max-width: 100%;
        }

        .toc-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
            color: var(--ebook-title-color);
            cursor: pointer;
        }

        .toc-head-right {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .toc-access-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 4px 9px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.04em;
            line-height: 1;
            text-transform: uppercase;
        }

        .toc-access-badge.preview {
            color: #0b7285;
            background: rgba(21, 170, 191, 0.16);
            border: 1px solid rgba(11, 114, 133, 0.22);
        }

        .toc-access-badge.locked {
            color: #8b5e00;
            background: rgba(255, 193, 7, 0.2);
            border: 1px solid rgba(139, 94, 0, 0.28);
        }

        .toc-access-badge.locked-link {
            text-decoration: none;
        }

        .toc-access-badge.locked-link:hover {
            color: #6f4b00;
            background: rgba(255, 193, 7, 0.3);
        }

        .toc-access-badge.full {
            color: #176a3a;
            background: rgba(25, 135, 84, 0.16);
            border: 1px solid rgba(23, 106, 58, 0.24);
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

        .toc-point-locked {
            color: #8b5e00;
            font-size: 12px;
            margin-left: 4px;
            vertical-align: middle;
        }

        .toc-locked-note {
            margin-top: 8px;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px dashed rgba(var(--ebook-primary-rgb), 0.28);
            background: rgba(var(--ebook-primary-rgb), 0.05);
            color: #486581;
            font-size: 13px;
            position: relative;
            z-index: 2;
        }

        .toc-body.locked-preview-area {
            position: relative;
            overflow: hidden;
        }

        .toc-locked-watermark {
            position: absolute;
            inset: 0;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }

        .toc-locked-watermark span {
            transform: rotate(-18deg);
            font-family: var(--ebook-title-font);
            font-size: clamp(18px, 4vw, 30px);
            font-weight: 800;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: rgba(var(--ebook-primary-rgb), 0.11);
            user-select: none;
            white-space: nowrap;
        }

        .hero-badge {
            background: rgba(var(--ebook-primary-rgb), 0.1);
            color: var(--ebook-title-color);
            border: 1px solid rgba(var(--ebook-primary-rgb), 0.12);
            font-size: 12px;
            font-weight: 600;
            border-radius: 999px;
            padding: 5px 10px;
            display: inline-block;
            margin-bottom: 10px;
        }

        .toc-list,
        .toc-list li,
        .toc-list a,
        p,
        .text-secondary,
        .text-muted {
            font-family: var(--ebook-body-font);
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
        <div class="pageTitle text-light">
            <img src="{{ asset('MySIFA.png') }}" alt="MySIFA" class="page-logo">
        </div>
        <div class="right">
            @if (!empty($isMember))
                <form method="POST" action="{{ route('member.logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="headerButton text-light border-0 bg-transparent" title="Logout Member">
                        <ion-icon name="log-out-outline"></ion-icon>
                    </button>
                </form>
            @else
                <a href="{{ route('member.login') }}" class="headerButton text-light" title="Login Member">
                    <ion-icon name="person-circle-outline"></ion-icon>
                </a>
            @endif
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
                    @php
                        $isChapterUnlocked = !empty($isMember) || $index === 0;
                    @endphp
                    <div class="toc-head" data-bs-toggle="collapse" data-bs-target="#chapter-{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}">
                        <div>
                            <span class="toc-no">{{ $index + 1 }}</span>
                            {{ $chapter['title'] }}
                        </div>
                        <div class="toc-head-right">
                            @if (!empty($isMember))
                                <span class="toc-access-badge full">Full Access</span>
                            @elseif ($index === 0)
                                <span class="toc-access-badge preview">Preview</span>
                            @else
                                <a href="{{ route('member.login') }}" class="toc-access-badge locked locked-link" onclick="event.stopPropagation();" title="Login member untuk buka bab ini">Locked</a>
                            @endif
                            <ion-icon name="chevron-down-outline"></ion-icon>
                        </div>
                    </div>
                    <div id="chapter-{{ $index }}" class="collapse {{ $index === 0 ? 'show' : '' }}">
                        <div class="toc-body {{ $isChapterUnlocked ? '' : 'locked-preview-area' }}">
                            @if ($isChapterUnlocked)
                                <ul class="toc-list">
                                    @foreach ($chapter['items'] as $item)
                                        <li>
                                            <a href="{{ route('ebook.point', ['slug' => $item['slug']]) }}" style="color: var(--ebook-primary); font-weight: 600;">
                                                {{ $item['title'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="toc-locked-watermark" aria-hidden="true">
                                    <span>Preview</span>
                                </div>
                                <ul class="toc-list">
                                    @foreach ($chapter['items'] as $item)
                                        <li>
                                            <a href="{{ route('ebook.point', ['slug' => $item['slug']]) }}" style="color: var(--ebook-primary); font-weight: 600;">
                                                {{ $item['title'] }}
                                            </a>
                                            <ion-icon name="lock-closed-outline" class="toc-point-locked"></ion-icon>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="toc-locked-note">
                                    Isi poin untuk bab ini terkunci. Silakan login atau daftar member untuk membuka konten lengkap.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="section mt-4">
            <div class="author-card">
                <div class="author-card-body">
                    <div class="author-grid">
                        <div class="author-photo-frame">
                            @if (!empty($content->author_photo))
                                <img src="{{ asset($content->author_photo) }}" alt="Foto Penulis">
                            @else
                                <div class="author-photo-placeholder">{{ $authorInitials }}</div>
                            @endif
                        </div>
                        <div class="author-copy">
                            <div class="author-eyebrow">Profil Penulis</div>
                            <h3 class="author-title">Tentang Penulis</h3>
                            <div class="author-name">{{ $authorName }}</div>
                            <p class="author-lead"></p>
                            <div class="rich-content">{!! $content->intro_note !!}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('Mobilekit/HTML/assets/js/lib/bootstrap.min.js') }}"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="{{ asset('Mobilekit/HTML/assets/js/base.js') }}"></script>
</body>

</html>
