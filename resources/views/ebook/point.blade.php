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

        .appHeader .right {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .member-session-name {
            display: inline-flex;
            align-items: center;
            max-width: 150px;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            color: #ffffff;
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.35);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
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

        .locked-content-box {
            border-radius: 14px;
            padding: 16px;
            border: 1px dashed rgba(var(--ebook-primary-rgb), 0.35);
            background: rgba(var(--ebook-primary-rgb), 0.05);
        }

        .locked-preview-watermark {
            position: absolute;
            inset: 0;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .locked-preview-watermark span {
            transform: rotate(-22deg);
            font-family: var(--ebook-title-font);
            font-weight: 800;
            letter-spacing: 0.14em;
            font-size: clamp(22px, 6vw, 42px);
            color: rgba(var(--ebook-primary-rgb), 0.14);
            text-transform: uppercase;
            white-space: nowrap;
            user-select: none;
        }

        .locked-content-title {
            font-family: var(--ebook-title-font);
            font-weight: 800;
            color: var(--ebook-title-color);
            margin-bottom: 8px;
        }

        .point-content img {
            max-width: 100%;
            height: auto;
            display: block;
            cursor: zoom-in;
        }

        .image-lightbox {
            position: fixed;
            inset: 0;
            z-index: 2000;
            display: none;
            background: rgba(10, 18, 28, 0.92);
        }

        .image-lightbox.is-open {
            display: block;
        }

        .image-lightbox-stage {
            position: absolute;
            inset: 0;
            overflow: hidden;
            touch-action: none;
        }

        .image-lightbox-stage img {
            position: absolute;
            left: 0;
            top: 0;
            transform-origin: 0 0;
            will-change: transform;
            border-radius: 10px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            -webkit-user-drag: none;
            user-select: none;
        }

        .image-lightbox-stage.is-zoomed img {
            cursor: grab;
        }

        .image-lightbox-stage.is-panning img {
            cursor: grabbing;
        }

        .image-lightbox-close {
            position: fixed;
            top: 16px;
            right: 16px;
            z-index: 2010;
            width: 42px;
            height: 42px;
            border-radius: 999px;
            border: 0;
            background: rgba(255, 255, 255, 0.14);
            color: #fff;
            font-size: 22px;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .image-lightbox-hint {
            position: fixed;
            left: 50%;
            bottom: 18px;
            transform: translateX(-50%);
            z-index: 2010;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.65);
            background: rgba(255, 255, 255, 0.1);
            padding: 6px 14px;
            border-radius: 999px;
            pointer-events: none;
            white-space: nowrap;
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

        .point-video-box {
            margin-top: 14px;
            margin-bottom: 14px;
            padding: 12px;
            border-radius: 14px;
            background: rgba(var(--ebook-primary-rgb), 0.08);
            border: 1px solid rgba(var(--ebook-primary-rgb), 0.12);
        }

        .point-video-title {
            font-family: var(--ebook-title-font);
            font-weight: 800;
            color: var(--ebook-title-color);
            margin-bottom: 8px;
        }

        .point-video-frame {
            position: relative;
            width: 100%;
            padding-top: 56.25%;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(16, 42, 67, 0.18);
            background: #000;
        }

        .point-video-frame iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
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

            .member-session-name {
                max-width: 95px;
                padding: 4px 8px;
                font-size: 11px;
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
        <div class="right">
            @if (!empty($isMember))
                <span class="member-session-name" title="{{ $memberName ?? '' }}">{{ $memberName ?? 'Member' }}</span>
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
        </div>
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
            <div class="point-content" style="position: relative;">
                @if (!empty($isPointLocked))
                    <div class="locked-preview-watermark" aria-hidden="true">
                        <span>Preview Mode</span>
                    </div>
                @endif

                @if (!empty($isPointLocked))
                    <div class="locked-content-box">
                        <div class="locked-content-title">Konten Terkunci untuk Member</div>
                        <p class="mb-2">Anda sudah bisa melihat judul poin, tetapi isi poin ini hanya tersedia untuk member aktif.</p>
                        <a href="{{ route('member.login') }}" class="btn btn-primary btn-sm me-1">Login Member</a>
                        <a href="{{ route('member.register') }}" class="btn btn-outline-primary btn-sm">Daftar Member</a>
                    </div>
                @else
                    @php
                        $normalizedPointContent = (string) ($point['content'] ?? '');
                        $normalizedPointContent = preg_replace(
                            '#https?://[^"\'<>]*/uploads/ebook-editor/#i',
                            '/uploads/ebook-editor/',
                            $normalizedPointContent
                        );
                    @endphp

                    @if ($normalizedPointContent !== '')
                        {!! $normalizedPointContent !!}
                    @else
                        <p class="mb-0">Konten untuk poin ini belum diisi. Silakan login admin untuk menambahkan isi halaman.</p>
                    @endif

                    @if (!empty($youtubeEmbedUrl))
                        <div class="point-video-box">
                            <div class="point-video-title">Video Pembelajaran</div>
                            <div class="point-video-frame">
                                <iframe
                                    src="{{ $youtubeEmbedUrl }}"
                                    title="Video YouTube - {{ $point['title'] }}"
                                    loading="lazy"
                                    referrerpolicy="strict-origin-when-cross-origin"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    allowfullscreen></iframe>
                            </div>
                        </div>
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

    <div class="image-lightbox" id="image-lightbox">
        <button type="button" class="image-lightbox-close" id="image-lightbox-close" aria-label="Tutup">&times;</button>
        <div class="image-lightbox-stage" id="image-lightbox-stage">
            <img src="" alt="" id="image-lightbox-img" draggable="false">
        </div>
        <div class="image-lightbox-hint">Cubit untuk zoom &bull; Geser untuk pindah &bull; Ketuk 2x untuk zoom cepat</div>
    </div>

    <script src="{{ asset('Mobilekit/HTML/assets/js/lib/bootstrap.min.js') }}"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="{{ asset('Mobilekit/HTML/assets/js/base.js') }}"></script>
    <script>
        (function () {
            const lightbox = document.getElementById('image-lightbox');
            const stage = document.getElementById('image-lightbox-stage');
            const lightboxImg = document.getElementById('image-lightbox-img');
            const lightboxClose = document.getElementById('image-lightbox-close');

            const MIN_SCALE = 1;
            const MAX_SCALE = 4;
            const DOUBLE_TAP_ZOOM = 3;
            const DOUBLE_TAP_DELAY = 300;
            const DOUBLE_TAP_DISTANCE = 36;
            const TAP_MAX_MOVEMENT = 10;

            let naturalW = 0;
            let naturalH = 0;
            let baseScale = 1;
            let scale = MIN_SCALE;
            let tx = 0;
            let ty = 0;

            const activePointers = new Map();
            let pinchPrevDistance = 0;
            let isPanning = false;
            let panLast = { x: 0, y: 0 };
            let gestureMoved = false;
            let tapStartPoint = null;
            let tapStartTarget = null;
            let lastTapTime = 0;
            let lastTapPoint = null;

            let rafScheduled = false;
            let animationFrameId = null;

            function clampScale(value) {
                return Math.min(MAX_SCALE, Math.max(MIN_SCALE, value));
            }

            function clampTranslate(txVal, tyVal, scaleVal) {
                const stageBox = stage.getBoundingClientRect();
                const finalScale = scaleVal * baseScale;
                const scaledW = naturalW * finalScale;
                const scaledH = naturalH * finalScale;

                const clampAxis = (t, scaledSize, stageSize) => {
                    if (scaledSize <= stageSize) {
                        return (stageSize - scaledSize) / 2;
                    }
                    return Math.min(0, Math.max(stageSize - scaledSize, t));
                };

                return {
                    x: clampAxis(txVal, scaledW, stageBox.width),
                    y: clampAxis(tyVal, scaledH, stageBox.height),
                };
            }

            function applyTransform() {
                lightboxImg.style.transform = `translate(${tx}px, ${ty}px) scale(${scale * baseScale})`;
                stage.classList.toggle('is-zoomed', scale > MIN_SCALE + 0.01);
            }

            function scheduleRender() {
                if (rafScheduled) {
                    return;
                }
                rafScheduled = true;
                requestAnimationFrame(() => {
                    rafScheduled = false;
                    applyTransform();
                });
            }

            function setTranslate(newTx, newTy, scaleVal) {
                const clamped = clampTranslate(newTx, newTy, scaleVal);
                tx = clamped.x;
                ty = clamped.y;
            }

            function computeZoomTarget(clientX, clientY, newScale) {
                newScale = clampScale(newScale);
                const stageBox = stage.getBoundingClientRect();
                const px = clientX - stageBox.left;
                const py = clientY - stageBox.top;
                const s0 = scale * baseScale;
                const s1 = newScale * baseScale;
                const rawTx = px - (px - tx) * (s1 / s0);
                const rawTy = py - (py - ty) * (s1 / s0);
                const clamped = clampTranslate(rawTx, rawTy, newScale);
                return { scale: newScale, tx: clamped.x, ty: clamped.y };
            }

            function zoomAt(clientX, clientY, newScale) {
                const target = computeZoomTarget(clientX, clientY, newScale);
                scale = target.scale;
                tx = target.tx;
                ty = target.ty;
            }

            function cancelAnimation() {
                if (animationFrameId) {
                    cancelAnimationFrame(animationFrameId);
                    animationFrameId = null;
                }
            }

            function animateTo(targetScale, anchorClientX, anchorClientY, duration = 220) {
                cancelAnimation();
                const target = computeZoomTarget(anchorClientX, anchorClientY, targetScale);
                const startScale = scale;
                const startTx = tx;
                const startTy = ty;
                const startTime = performance.now();

                function step(now) {
                    const elapsed = now - startTime;
                    const progress = Math.min(1, elapsed / duration);
                    const eased = 1 - Math.pow(1 - progress, 3);

                    scale = startScale + (target.scale - startScale) * eased;
                    tx = startTx + (target.tx - startTx) * eased;
                    ty = startTy + (target.ty - startTy) * eased;

                    applyTransform();

                    if (progress < 1) {
                        animationFrameId = requestAnimationFrame(step);
                    } else {
                        animationFrameId = null;
                    }
                }

                animationFrameId = requestAnimationFrame(step);
            }

            function resetGestureState() {
                cancelAnimation();
                activePointers.clear();
                isPanning = false;
                pinchPrevDistance = 0;
                gestureMoved = false;
                tapStartPoint = null;
                tapStartTarget = null;
                lastTapTime = 0;
                lastTapPoint = null;
                stage.classList.remove('is-panning', 'is-zoomed');
            }

            function setupStage() {
                const stageBox = stage.getBoundingClientRect();
                baseScale = Math.min(stageBox.width / naturalW, stageBox.height / naturalH, 1);
                lightboxImg.style.width = naturalW + 'px';
                lightboxImg.style.height = naturalH + 'px';
                scale = MIN_SCALE;
                const centered = clampTranslate(0, 0, scale);
                tx = centered.x;
                ty = centered.y;
                applyTransform();
            }

            function openLightbox(src, alt) {
                resetGestureState();
                naturalW = 0;
                naturalH = 0;

                lightboxImg.onload = () => {
                    naturalW = lightboxImg.naturalWidth || 1;
                    naturalH = lightboxImg.naturalHeight || 1;
                    setupStage();
                };
                lightboxImg.alt = alt || '';
                lightboxImg.src = src;

                lightbox.classList.add('is-open');
                document.body.style.overflow = 'hidden';
            }

            function closeLightbox() {
                resetGestureState();
                lightbox.classList.remove('is-open');
                lightboxImg.removeAttribute('src');
                lightboxImg.style.transform = '';
                lightboxImg.style.width = '';
                lightboxImg.style.height = '';
                document.body.style.overflow = '';
            }

            function getDistance(p1, p2) {
                return Math.hypot(p1.x - p2.x, p1.y - p2.y);
            }

            function getMidpoint(p1, p2) {
                return { x: (p1.x + p2.x) / 2, y: (p1.y + p2.y) / 2 };
            }

            function handleTap(x, y, target) {
                const now = Date.now();
                const isDoubleTap = lastTapPoint
                    && (now - lastTapTime) < DOUBLE_TAP_DELAY
                    && getDistance({ x, y }, lastTapPoint) < DOUBLE_TAP_DISTANCE;

                if (isDoubleTap) {
                    lastTapTime = 0;
                    lastTapPoint = null;

                    if (scale > MIN_SCALE + 0.01) {
                        animateTo(MIN_SCALE, x, y);
                    } else {
                        animateTo(DOUBLE_TAP_ZOOM, x, y);
                    }
                    return;
                }

                lastTapTime = now;
                lastTapPoint = { x, y };

                if (target !== lightboxImg) {
                    setTimeout(() => {
                        if (lastTapTime === now) {
                            closeLightbox();
                        }
                    }, DOUBLE_TAP_DELAY);
                }
            }

            stage.addEventListener('pointerdown', (event) => {
                stage.setPointerCapture(event.pointerId);
                activePointers.set(event.pointerId, { x: event.clientX, y: event.clientY });
                gestureMoved = false;
                tapStartPoint = { x: event.clientX, y: event.clientY };
                tapStartTarget = event.target;
                cancelAnimation();

                if (activePointers.size === 2) {
                    isPanning = false;
                    const pts = Array.from(activePointers.values());
                    pinchPrevDistance = getDistance(pts[0], pts[1]);
                } else if (activePointers.size === 1) {
                    isPanning = true;
                    panLast = { x: event.clientX, y: event.clientY };
                    stage.classList.add('is-panning');
                }
            });

            stage.addEventListener('pointermove', (event) => {
                if (!activePointers.has(event.pointerId)) {
                    return;
                }
                activePointers.set(event.pointerId, { x: event.clientX, y: event.clientY });

                if (tapStartPoint) {
                    const moved = getDistance({ x: event.clientX, y: event.clientY }, tapStartPoint);
                    if (moved > TAP_MAX_MOVEMENT) {
                        gestureMoved = true;
                    }
                }

                if (activePointers.size === 2) {
                    const pts = Array.from(activePointers.values());
                    const newDistance = getDistance(pts[0], pts[1]);
                    const mid = getMidpoint(pts[0], pts[1]);

                    if (pinchPrevDistance > 0) {
                        const ratio = newDistance / pinchPrevDistance;
                        zoomAt(mid.x, mid.y, clampScale(scale * ratio));
                    }
                    pinchPrevDistance = newDistance;
                    scheduleRender();
                } else if (activePointers.size === 1 && isPanning && scale > MIN_SCALE + 0.001) {
                    const dx = event.clientX - panLast.x;
                    const dy = event.clientY - panLast.y;
                    panLast = { x: event.clientX, y: event.clientY };
                    setTranslate(tx + dx, ty + dy, scale);
                    scheduleRender();
                }
            });

            function onPointerEnd(event) {
                activePointers.delete(event.pointerId);
                try {
                    stage.releasePointerCapture(event.pointerId);
                } catch (error) {
                    // ignore
                }

                if (activePointers.size === 1) {
                    const remaining = Array.from(activePointers.values())[0];
                    panLast = remaining;
                    isPanning = true;
                    pinchPrevDistance = 0;
                } else if (activePointers.size === 0) {
                    isPanning = false;
                    stage.classList.remove('is-panning');

                    if (!gestureMoved && tapStartPoint) {
                        handleTap(tapStartPoint.x, tapStartPoint.y, tapStartTarget);
                    }
                    tapStartPoint = null;
                    tapStartTarget = null;
                }
            }

            stage.addEventListener('pointerup', onPointerEnd);
            stage.addEventListener('pointercancel', onPointerEnd);

            stage.addEventListener('wheel', (event) => {
                if (!event.ctrlKey) {
                    return;
                }
                event.preventDefault();
                cancelAnimation();
                const zoomFactor = Math.exp(-event.deltaY * 0.01);
                zoomAt(event.clientX, event.clientY, clampScale(scale * zoomFactor));
                scheduleRender();
            }, { passive: false });

            window.addEventListener('resize', () => {
                if (lightbox.classList.contains('is-open') && naturalW && naturalH) {
                    setupStage();
                }
            });

            document.querySelectorAll('.point-content img').forEach((img) => {
                img.addEventListener('click', () => openLightbox(img.currentSrc || img.src, img.alt));
            });

            lightboxClose.addEventListener('click', closeLightbox);

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeLightbox();
                }
            });
        })();
    </script>
</body>

</html>
