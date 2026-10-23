{{-- Tombol WhatsApp melayang. Pakai: @include('partials.wa-help', ['link' => $waHelpLink, 'label' => 'Butuh bantuan?']) --}}
@if (!empty($link))
    <a href="{{ $link }}" target="_blank" rel="noopener" class="wa-float" aria-label="Chat WhatsApp">
        <svg viewBox="0 0 32 32" width="22" height="22" aria-hidden="true" fill="currentColor">
            <path d="M16.02 3C8.84 3 3 8.83 3 16c0 2.3.6 4.53 1.75 6.5L3 29l6.68-1.73A12.95 12.95 0 0 0 16.02 29C23.2 29 29 23.17 29 16S23.2 3 16.02 3zm0 23.6c-2.03 0-4.02-.55-5.75-1.6l-.41-.24-3.96 1.03 1.06-3.86-.27-.4A10.56 10.56 0 0 1 5.4 16c0-5.85 4.77-10.6 10.62-10.6 5.84 0 10.6 4.75 10.6 10.6 0 5.85-4.76 10.6-10.6 10.6zm5.82-7.94c-.32-.16-1.88-.93-2.17-1.03-.29-.11-.5-.16-.71.16-.21.32-.82 1.03-1 1.24-.19.21-.37.24-.69.08-.32-.16-1.34-.5-2.56-1.58-.95-.84-1.59-1.88-1.77-2.2-.19-.32-.02-.49.14-.65.14-.14.32-.37.48-.56.16-.19.21-.32.32-.53.1-.21.05-.4-.03-.56-.08-.16-.71-1.71-.98-2.34-.26-.62-.52-.53-.71-.54h-.61c-.21 0-.56.08-.85.4-.29.32-1.11 1.08-1.11 2.64 0 1.56 1.14 3.07 1.3 3.28.16.21 2.24 3.42 5.43 4.8.76.33 1.35.52 1.81.67.76.24 1.45.2 2 .12.61-.09 1.88-.77 2.14-1.51.27-.74.27-1.38.19-1.51-.08-.13-.29-.21-.61-.37z"/>
        </svg>
        <span>{{ $label ?? 'Butuh bantuan? Chat WhatsApp' }}</span>
    </a>
    <style>
        .wa-float {
            position: fixed;
            right: 16px;
            bottom: 16px;
            z-index: 1050;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 999px;
            background: #25d366;
            color: #fff !important;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.22);
        }

        .wa-float:hover {
            background: #1ebe5b;
        }
    </style>
@endif
