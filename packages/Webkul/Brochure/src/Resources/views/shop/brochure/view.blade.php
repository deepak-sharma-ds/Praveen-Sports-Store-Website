<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ core()->getCurrentLocale()->direction ?? 'ltr' }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="robots" content="index,follow" />
    <title>{{ $brochure->meta_title ?: $brochure->title }} — {{ config('app.name') }}</title>

    @if ($brochure->meta_description)
        <meta name="description" content="{{ $brochure->meta_description }}" />
    @endif

    {{-- Poppins — matches brand typography --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />

    {{-- PDF.js (page rendering for PDF mode) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js" defer></script>

    {{-- StPageFlip (flipbook engine, open-source, no jQuery) --}}
    <script src="https://unpkg.com/page-flip@2.0.7/dist/js/page-flip.browser.js" defer></script>

    <style>
        /* ==============================================================
         * PRAVEEN SPORTS — BROCHURE FLIPBOOK VIEWER
         * Brand: Navy #060C3B | Maroon #902129 | Cream #F6F2EB
         * ============================================================== */

        /* ── Reset & base ─────────────────────────────────────────── */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            height: 100%;
            /* Deep brand navy — premium dark feel */
            background: #04082f;
            font-family: 'Poppins', system-ui, -apple-system, sans-serif;
            font-size: 14px;
            overflow: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* ── CSS variables ────────────────────────────────────────── */
        :root {
            --topbar-h:   56px;
            --controls-h: 66px;
            --navy:       #060C3B;
            --navy-glass: rgba(6, 12, 59, 0.88);
            --maroon:     #902129;
            --maroon-glow:rgba(144, 33, 41, 0.18);
            --white-dim:  rgba(255, 255, 255, 0.72);
            --white-faint:rgba(255, 255, 255, 0.1);
            --border:     rgba(255, 255, 255, 0.08);
            --border-m:   rgba(144, 33, 41, 0.28);
        }

        /* ── Top navigation bar ───────────────────────────────────── */
        #brochure-topbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            height: var(--topbar-h);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.25rem;
            background: var(--navy-glass);
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            border-bottom: 1px solid var(--border);
            /* Subtle maroon accent on bottom edge */
            box-shadow: 0 1px 0 0 var(--border-m),
                        0 4px 20px rgba(0,0,0,0.3);
        }

        /* Left side: accent bar + title */
        .nav-left {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            min-width: 0;
            flex: 1;
        }

        /* Vertical maroon accent stripe */
        .nav-brand-accent {
            width: 3px;
            height: 22px;
            background: linear-gradient(180deg, #c0392b, var(--maroon));
            border-radius: 2px;
            flex-shrink: 0;
        }

        .nav-store-name {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.35);
            white-space: nowrap;
            flex-shrink: 0;
            display: none; /* hidden on small; shown md+ */
        }

        .nav-divider {
            width: 1px;
            height: 14px;
            background: var(--border);
            flex-shrink: 0;
            display: none;
        }

        #brochure-topbar .nav-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: rgba(255,255,255,0.88);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Right side: action buttons */
        #brochure-topbar .nav-actions {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            flex-shrink: 0;
        }

        /* Glassmorphism pill buttons */
        .nav-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.38rem;
            padding: 0.38rem 0.82rem;
            background: var(--white-faint);
            border: 1px solid var(--border);
            color: var(--white-dim);
            font-family: inherit;
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 20px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s, color 0.2s, border-color 0.2s, transform 0.15s;
            white-space: nowrap;
        }

        .nav-btn:hover {
            background: rgba(255,255,255,0.16);
            border-color: rgba(255,255,255,0.22);
            color: #fff;
            transform: translateY(-1px);
        }

        .nav-btn:active {
            transform: translateY(0);
        }

        .nav-btn svg { flex-shrink: 0; }

        /* Back button — maroon accent */
        .nav-btn--back:hover {
            background: rgba(144,33,41,0.2);
            border-color: rgba(144,33,41,0.4);
        }

        /* Download button — green accent */
        .nav-btn--download {
            border-color: rgba(34,197,94,0.22);
        }

        .nav-btn--download:hover {
            background: rgba(34,197,94,0.15);
            border-color: rgba(34,197,94,0.4);
            color: #86efac;
        }

        /* ── Main stage ───────────────────────────────────────────── */
        #brochure-stage {
            position: fixed;
            inset: var(--topbar-h) 0 var(--controls-h) 0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            /* Subtle radial background gradient */
            background:
                radial-gradient(ellipse 80% 60% at 50% 50%, rgba(13,26,94,0.6) 0%, transparent 70%),
                #04082f;
        }

        /* Ambient glow from the book — maroon tinted halo */
        #book-ambient-glow {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 70%;
            max-width: 800px;
            height: 55%;
            background: radial-gradient(
                ellipse at center,
                rgba(144, 33, 41, 0.12) 0%,
                rgba(6, 12, 59, 0.06) 45%,
                transparent 72%
            );
            pointer-events: none;
            z-index: 0;
            filter: blur(30px);
        }

        /* 3D perspective wrapper for the book */
        #book-stage-perspective {
            position: relative;
            z-index: 1;
            /* Subtle tilt for depth — like book resting on table */
            transform: perspective(2800px) rotateX(1.2deg);
            transform-style: preserve-3d;
            transition: transform 0.4s ease;
            filter: drop-shadow(0 28px 60px rgba(0,0,0,0.55))
                    drop-shadow(0 8px 20px rgba(0,0,0,0.35));
        }

        /* The flipbook container (StPageFlip mounts here — DO NOT rename) */
        #flipbook-container {
            position: relative;
        }

        /* Page canvas base style */
        .page-canvas {
            display: block;
            background: #fff;
        }

        /* ── Loader ───────────────────────────────────────────────── */
        #brochure-loader {
            position: fixed;
            inset: 0;
            z-index: 200;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            background: #04082f;
        }

        /* Animated book with brand maroon + navy colors */
        .loader-book {
            width: 64px;
            height: 84px;
            position: relative;
            perspective: 300px;
        }

        .loader-spine {
            position: absolute;
            top: 0;
            left: 0;
            width: 50%;
            height: 100%;
            background: linear-gradient(180deg, #6b0e14 0%, var(--maroon) 50%, #6b0e14 100%);
            border-radius: 5px 0 0 5px;
        }

        .loader-page {
            position: absolute;
            top: 0;
            right: 0;
            width: 50%;
            height: 100%;
            background: linear-gradient(135deg, #1a2a8a 0%, #0d1a5e 100%);
            border-radius: 0 5px 5px 0;
            transform-origin: left center;
            animation: loaderFlip 1.4s cubic-bezier(0.4, 0, 0.2, 1) infinite alternate;
        }

        @keyframes loaderFlip {
            0%   { transform: rotateY(0deg);    }
            35%  { transform: rotateY(-5deg);   }
            100% { transform: rotateY(-165deg); }
        }

        .loader-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.3rem;
        }

        .loader-text {
            color: rgba(255,255,255,0.55);
            font-size: 0.8rem;
            font-weight: 500;
            letter-spacing: 0.04em;
        }

        .loader-progress {
            width: 210px;
            height: 3px;
            background: rgba(255,255,255,0.08);
            border-radius: 2px;
            overflow: hidden;
        }

        .loader-progress-bar {
            height: 100%;
            width: 0%;
            /* Brand maroon gradient */
            background: linear-gradient(90deg, var(--maroon), #c0392b);
            border-radius: 2px;
            transition: width 0.35s ease;
        }

        /* ── Bottom controls ──────────────────────────────────────── */
        #brochure-controls {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 100;
            height: var(--controls-h);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            background: var(--navy-glass);
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            border-top: 1px solid var(--border);
            box-shadow: 0 -1px 0 0 var(--border-m),
                        0 -4px 20px rgba(0,0,0,0.25);
        }

        /* Icon control buttons */
        .ctrl-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: var(--white-faint);
            border: 1px solid var(--border);
            color: var(--white-dim);
            border-radius: 50%;
            cursor: pointer;
            transition: background 0.18s, color 0.18s, border-color 0.18s, transform 0.15s;
        }

        .ctrl-btn:hover:not(:disabled) {
            background: rgba(255,255,255,0.16);
            border-color: rgba(255,255,255,0.22);
            color: #fff;
            transform: scale(1.1);
        }

        .ctrl-btn:active:not(:disabled) {
            transform: scale(0.95);
        }

        .ctrl-btn:disabled {
            opacity: 0.22;
            cursor: not-allowed;
        }

        /* Next / Prev are slightly larger (primary actions) */
        #btn-prev, #btn-next {
            width: 44px;
            height: 44px;
            /* Maroon accent on primary nav buttons */
            border-color: rgba(144,33,41,0.3);
        }

        #btn-prev:hover:not(:disabled),
        #btn-next:hover:not(:disabled) {
            background: rgba(144,33,41,0.22);
            border-color: rgba(144,33,41,0.5);
            color: #fff;
        }

        /* Page indicator — pill badge */
        #page-indicator {
            background: var(--white-faint);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 0.28rem 1.1rem;
            color: rgba(255,255,255,0.78);
            font-size: 0.78rem;
            font-weight: 500;
            letter-spacing: 0.03em;
            min-width: 110px;
            text-align: center;
            font-variant-numeric: tabular-nums;
        }

        /* ── Zoom overlay ─────────────────────────────────────────── */
        #zoom-overlay {
            position: fixed;
            inset: 0;
            z-index: 300;
            background: rgba(4, 8, 47, 0.96);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            cursor: zoom-out;
        }

        #zoom-overlay.active { display: flex; }

        #zoom-canvas {
            max-width: 88vw;
            max-height: 84vh;
            object-fit: contain;
            border-radius: 4px;
            box-shadow:
                0 40px 80px rgba(0,0,0,0.7),
                0 0 0 1px rgba(144,33,41,0.2);
        }

        .zoom-close-hint {
            color: rgba(255,255,255,0.3);
            font-size: 0.72rem;
            letter-spacing: 0.06em;
        }

        /* ── Toast ────────────────────────────────────────────────── */
        #brochure-toast {
            position: fixed;
            bottom: calc(var(--controls-h) + 12px);
            left: 50%;
            transform: translateX(-50%) translateY(14px);
            background: rgba(6, 12, 59, 0.9);
            border: 1px solid var(--border-m);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            color: rgba(255,255,255,0.85);
            padding: 0.45rem 1.2rem;
            border-radius: 20px;
            font-size: 0.76rem;
            font-weight: 500;
            opacity: 0;
            transition: opacity 0.28s, transform 0.28s;
            pointer-events: none;
            white-space: nowrap;
            z-index: 150;
        }

        #brochure-toast.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        /* ── Keyboard hint (below controls) ──────────────────────── */
        #keyboard-hint {
            position: fixed;
            bottom: 4px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.6rem;
            color: rgba(255,255,255,0.18);
            letter-spacing: 0.05em;
            pointer-events: none;
            z-index: 101;
            white-space: nowrap;
        }

        /* ── Responsive ───────────────────────────────────────────── */
        @media (min-width: 768px) {
            .nav-store-name { display: block; }
            .nav-divider     { display: block; }
        }

        @media (max-width: 768px) {
            #brochure-topbar .nav-btn span { display: none; }
            .nav-btn { padding: 0.38rem 0.55rem; border-radius: 50%; }
            #brochure-controls { gap: 0.45rem; }
            #keyboard-hint { display: none; }
        }
    </style>
</head>
<body>

    {{-- ── PHP data → JavaScript ──────────────────────────────────────── --}}
    <script>
        window.BrochureData = {
            mode:        @json($brochure->type),
            pdfUrl:      @json($brochure->pdf_url),
            pages:       @json($brochure->page_images),
            title:       @json($brochure->title),
            initialPage: @json($initialPage ?? 1),
            slug:        @json($brochure->slug),
            listingUrl:  @json(route('shop.brochure.index')),
        };
    </script>

    {{-- ── Loader ──────────────────────────────────────────────────────── --}}
    <div id="brochure-loader" role="status" aria-label="Loading brochure">
        <div class="loader-book" aria-hidden="true">
            <div class="loader-spine"></div>
            <div class="loader-page"></div>
        </div>
        <div class="loader-label">
            <span class="loader-text" id="loader-text">Loading brochure…</span>
            <div class="loader-progress">
                <div class="loader-progress-bar" id="loader-progress-bar"></div>
            </div>
        </div>
    </div>

    {{-- ── Top Navigation ───────────────────────────────────────────────── --}}
    <header id="brochure-topbar">
        <div class="nav-left">
            <div class="nav-brand-accent" aria-hidden="true"></div>
            <span class="nav-store-name">Praveen Sports</span>
            <span class="nav-divider" aria-hidden="true"></span>
            <span class="nav-title">{{ $brochure->title }}</span>
        </div>

        <div class="nav-actions">
            {{-- Zoom --}}
            <button class="nav-btn" id="btn-zoom" title="Zoom current page (Z)" aria-label="Zoom current page">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35M11 8v6M8 11h6"/>
                </svg>
                <span>Zoom</span>
            </button>

            {{-- Sound toggle --}}
            <button class="nav-btn" id="btn-sound" title="Toggle page-flip sound" aria-label="Toggle sound">
                <svg id="sound-icon-on" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/>
                    <path d="M15.54 8.46a5 5 0 0 1 0 7.07"/>
                    <path d="M19.07 4.93a10 10 0 0 1 0 14.14"/>
                </svg>
                <svg id="sound-icon-off" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="display:none">
                    <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/>
                    <line x1="23" y1="9" x2="17" y2="15"/>
                    <line x1="17" y1="9" x2="23" y2="15"/>
                </svg>
                <span>Sound</span>
            </button>

            {{-- Download PDF --}}
            @if ($brochure->pdf_url)
            <a href="{{ $brochure->pdf_url }}"
               download="{{ \Illuminate\Support\Str::slug($brochure->title) }}.pdf"
               class="nav-btn nav-btn--download"
               title="Download PDF"
               aria-label="Download PDF">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                <span>Download</span>
            </a>
            @endif

            {{-- Back to catalog --}}
            <a href="{{ route('shop.brochure.index') }}" class="nav-btn nav-btn--back" aria-label="Back to all brochures">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="m15 18-6-6 6-6"/>
                </svg>
                <span>All Brochures</span>
            </a>
        </div>
    </header>

    {{-- ── Flipbook Stage ───────────────────────────────────────────────── --}}
    <main id="brochure-stage">
        {{-- Ambient glow behind the book --}}
        <div id="book-ambient-glow" aria-hidden="true"></div>

        {{-- 3D perspective wrapper (does NOT interfere with StPageFlip) --}}
        <div id="book-stage-perspective">
            {{-- StPageFlip mounts into this exact element --}}
            <div id="flipbook-container"></div>
        </div>
    </main>

    {{-- ── Bottom Controls ─────────────────────────────────────────────── --}}
    <nav id="brochure-controls" aria-label="Page navigation">
        <button class="ctrl-btn" id="btn-first" title="First page (Home)" aria-label="First page">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <polyline points="11 17 6 12 11 7"/>
                <polyline points="18 17 13 12 18 7"/>
            </svg>
        </button>

        <button class="ctrl-btn" id="btn-prev" title="Previous page (←)" aria-label="Previous page">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
        </button>

        <span id="page-indicator" aria-live="polite" aria-atomic="true">— / —</span>

        <button class="ctrl-btn" id="btn-next" title="Next page (→)" aria-label="Next page">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                <polyline points="9 18 15 12 9 6"/>
            </svg>
        </button>

        <button class="ctrl-btn" id="btn-last" title="Last page (End)" aria-label="Last page">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <polyline points="13 17 18 12 13 7"/>
                <polyline points="6 17 11 12 6 7"/>
            </svg>
        </button>
    </nav>

    {{-- Keyboard shortcut hint --}}
    <div id="keyboard-hint" aria-hidden="true">← → Arrow keys to flip &nbsp;·&nbsp; Home / End &nbsp;·&nbsp; Z to zoom &nbsp;·&nbsp; Esc to close zoom &nbsp;·&nbsp; D to download</div>

    {{-- ── Zoom overlay ────────────────────────────────────────────────── --}}
    <div id="zoom-overlay" role="dialog" aria-modal="true" aria-label="Page zoom view">
        <canvas id="zoom-canvas"></canvas>
        <span class="zoom-close-hint">Click anywhere or press Esc to close</span>
    </div>

    {{-- ── Toast ───────────────────────────────────────────────────────── --}}
    <div id="brochure-toast" role="status" aria-live="polite"></div>

    {{-- Hidden audio element (kept for JS reference — sound generated via Web Audio API) --}}
    <audio id="flip-sound" preload="none" style="display:none" aria-hidden="true"></audio>

    {{-- ==============================================================
         BROCHURE FLIPBOOK ENGINE — Logic unchanged from original
         ============================================================== --}}
    <script>
    /**
     * BrochureFlipbook — orchestrates StPageFlip + PDF.js
     * Supports: PDF mode (lazy render) and Image mode (webp)
     * Features: lazy loading, deep linking, zoom, flip sound, analytics
     */
    (function () {
        'use strict';

        const DATA         = window.BrochureData;
        const container    = document.getElementById('flipbook-container');
        const loader       = document.getElementById('brochure-loader');
        const loaderText   = document.getElementById('loader-text');
        const progressBar  = document.getElementById('loader-progress-bar');
        const indicator    = document.getElementById('page-indicator');
        const btnFirst     = document.getElementById('btn-first');
        const btnPrev      = document.getElementById('btn-prev');
        const btnNext      = document.getElementById('btn-next');
        const btnLast      = document.getElementById('btn-last');
        const btnZoom      = document.getElementById('btn-zoom');
        const btnSound     = document.getElementById('btn-sound');
        const soundIconOn  = document.getElementById('sound-icon-on');
        const soundIconOff = document.getElementById('sound-icon-off');
        const zoomOverlay  = document.getElementById('zoom-overlay');
        const zoomCanvas   = document.getElementById('zoom-canvas');
        const flipAudio    = document.getElementById('flip-sound');

        // ── State ──────────────────────────────────────────────────────
        let pageFlip      = null;
        let pdfDoc        = null;
        let totalPages    = 0;
        let currentPage   = DATA.initialPage || 1;
        let soundEnabled  = true;
        let renderedPages = {};  // { pageIndex: true } — tracks rendered PDF pages
        let isFlipping    = false;

        // ── Helpers ────────────────────────────────────────────────────

        function isMobile() {
            return window.innerWidth < 768;
        }

        function setProgress(pct, text) {
            progressBar.style.width = pct + '%';
            if (text) loaderText.textContent = text;
        }

        function hideLoader() {
            loader.style.transition = 'opacity 0.4s';
            loader.style.opacity    = '0';
            setTimeout(() => { loader.style.display = 'none'; }, 400);
        }

        function showToast(msg, duration = 2000) {
            const toast = document.getElementById('brochure-toast');
            toast.textContent = msg;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), duration);
        }

        function updateIndicator(page, total) {
            indicator.textContent = 'Page ' + page + ' of ' + total;
            btnFirst.disabled = page <= 1;
            btnPrev.disabled  = page <= 1;
            btnNext.disabled  = page >= total;
            btnLast.disabled  = page >= total;

            // Deep-link: update URL without reload
            const url = new URL(window.location.href);
            url.searchParams.set('page', page);
            window.history.replaceState({}, '', url.toString());
        }

        function playFlipSound() {
            if (!soundEnabled || !flipAudio) return;
            try {
                // Use Web Audio API for a crisp click sound without external files
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const buf = ctx.createBuffer(1, ctx.sampleRate * 0.08, ctx.sampleRate);
                const data = buf.getChannelData(0);
                for (let i = 0; i < data.length; i++) {
                    // Exponential decay noise burst — sounds like paper
                    data[i] = (Math.random() * 2 - 1) * Math.pow(1 - i / data.length, 3);
                }
                const src = ctx.createBufferSource();
                src.buffer = buf;
                const gain = ctx.createGain();
                gain.gain.value = 0.35;
                src.connect(gain);
                gain.connect(ctx.destination);
                src.start();
                src.onended = () => ctx.close();
            } catch (_) {}
        }

        // ── Compute flipbook dimensions ────────────────────────────────

        function getFlipDimensions() {
            const stageW = window.innerWidth;
            const stageH = window.innerHeight - 56 - 66; // topbar + controls heights
            const ratio  = 1.414; // A4 aspect ratio

            if (isMobile()) {
                // Single-page mode on mobile — fill nearly full stage width
                const w = Math.min(stageW * 0.97, 520);
                return { width: Math.round(w), height: Math.round(w * ratio) };
            }

            // Desktop: two-page spread — fill 96% of available space
            const maxW = stageW * 0.96;
            const maxH = stageH * 0.96;
            let   w    = maxW / 2; // per-page width
            let   h    = w * ratio;

            if (h > maxH) {
                h = maxH;
                w = h / ratio;
            }

            return { width: Math.round(w), height: Math.round(h) };
        }

        // ── PAGE FLIP COMMON INIT ──────────────────────────────────────

        function initPageFlipInstance(elements) {
            const { width, height } = getFlipDimensions();

            pageFlip = new St.PageFlip(container, {
                width:               width,
                height:              height,
                size:                'fixed',
                minWidth:            200,
                maxWidth:            800,
                minHeight:           280,
                maxHeight:           1200,
                drawShadow:          true,
                flippingTime:        700,
                usePortrait:         isMobile(),
                startZIndex:         0,
                autoSize:            false,
                maxShadowOpacity:    0.6,
                showCover:           true,
                mobileScrollSupport: false,
                swipeDistance:       30,
                clickEventForward:   true,
                useMouseEvents:      true,
            });

            pageFlip.loadFromHTML(elements);

            // Listen for flip to update indicator + trigger lazy loading
            pageFlip.on('flip', function (e) {
                currentPage = e.data + 1;
                updateIndicator(currentPage, totalPages);
                playFlipSound();

                // Lazy-load nearby pages for PDF mode
                if (DATA.mode === 'pdf') {
                    const ahead = 3;
                    renderPdfPages(
                        Math.max(0, e.data - 1),
                        Math.min(totalPages - 1, e.data + ahead)
                    );
                }

                // Analytics tracking (non-blocking)
                trackPageView(currentPage);
            });

            // Handle orientation / resize
            window.addEventListener('resize', debounce(function () {
                if (pageFlip) {
                    const d = getFlipDimensions();
                    pageFlip.updateOrientation(isMobile() ? 'portrait' : 'landscape');
                }
            }, 300));

            return pageFlip;
        }

        // ── MODE 1: PDF RENDERING ─────────────────────────────────────

        async function initPdfMode() {
            if (!DATA.pdfUrl) {
                hideLoader();
                showToast('No PDF file available for this brochure.', 4000);
                return;
            }

            setProgress(10, 'Loading PDF…');

            // Configure PDF.js worker
            pdfjsLib.GlobalWorkerOptions.workerSrc =
                'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

            try {
                pdfDoc     = await pdfjsLib.getDocument({
                    url:                 DATA.pdfUrl,
                    cMapUrl:             'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/cmaps/',
                    cMapPacked:          true,
                    standardFontDataUrl: 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/standard_fonts/',
                }).promise;
                totalPages = pdfDoc.numPages;

                setProgress(20, 'Preparing pages…');

                // Build canvas elements for every page (blank initially)
                const pages = [];

                for (let i = 1; i <= totalPages; i++) {
                    const canvas = document.createElement('canvas');
                    canvas.classList.add('page-canvas');
                    canvas.id = 'pdf-page-' + i;
                    canvas.dataset.pageNum = i;
                    pages.push(canvas);
                }

                // Size all canvases from page 1 viewport — DPR-aware for crisp text
                const firstPage = await pdfDoc.getPage(1);
                const dpr       = window.devicePixelRatio || 1;
                const baseScale = isMobile() ? 1.5 : 2.5;
                const scale     = baseScale * dpr;
                const viewport  = firstPage.getViewport({ scale });
                const logicalW  = Math.round(viewport.width  / dpr);
                const logicalH  = Math.round(viewport.height / dpr);

                pages.forEach(c => {
                    c.width        = viewport.width;   // hi-res pixel buffer
                    c.height       = viewport.height;
                    c.style.width  = logicalW + 'px';  // CSS display at logical size
                    c.style.height = logicalH + 'px';
                });

                initPageFlipInstance(pages);

                setProgress(40, 'Rendering first pages…');

                // Render first 4 pages eagerly, rest on demand
                const eagerCount = Math.min(4, totalPages);
                await renderPdfPages(0, eagerCount - 1, true);

                setProgress(100, 'Ready!');

                updateIndicator(currentPage, totalPages);

                // Jump to deep-linked page
                if (currentPage > 1 && pageFlip) {
                    pageFlip.turnToPage(currentPage - 1);
                }

                hideLoader();

                // Background-render remaining pages after a short delay
                setTimeout(() => renderPdfPages(eagerCount, totalPages - 1), 1500);

            } catch (err) {
                console.error('[Brochure] PDF load error:', err);
                setProgress(100, 'Failed to load PDF.');
                showToast('Could not load PDF. Please try again.', 5000);
                setTimeout(hideLoader, 2000);
            }
        }

        /**
         * Render a range of PDF pages [fromIdx, toIdx] (0-based indices).
         * Skips already-rendered pages. Reports progress when withProgress=true.
         */
        async function renderPdfPages(fromIdx, toIdx, withProgress = false) {
            if (!pdfDoc) return;

            for (let i = fromIdx; i <= toIdx; i++) {
                if (renderedPages[i]) continue;

                renderedPages[i] = true; // mark immediately to prevent duplicate renders

                try {
                    const pageNum   = i + 1;
                    const page      = await pdfDoc.getPage(pageNum);
                    const canvas    = document.getElementById('pdf-page-' + pageNum);

                    if (!canvas) continue;

                    const dpr       = window.devicePixelRatio || 1;
                    const baseScale = isMobile() ? 1.5 : 2.5;
                    const scale     = baseScale * dpr;
                    const viewport  = page.getViewport({ scale });

                    canvas.width        = viewport.width;
                    canvas.height       = viewport.height;
                    canvas.style.width  = Math.round(viewport.width  / dpr) + 'px';
                    canvas.style.height = Math.round(viewport.height / dpr) + 'px';

                    const ctx = canvas.getContext('2d');

                    await page.render({ canvasContext: ctx, viewport }).promise;

                    if (withProgress) {
                        const rendered = Object.keys(renderedPages).length;
                        const pct      = 40 + Math.round((rendered / Math.min(4, totalPages)) * 55);
                        setProgress(Math.min(pct, 95), 'Rendering page ' + pageNum + '…');
                    }
                } catch (e) {
                    console.warn('[Brochure] Page render failed:', i + 1, e);
                    renderedPages[i] = false; // allow retry
                }
            }
        }

        // ── MODE 2: IMAGE (WebP) ─────────────────────────────────────

        function initImageMode() {
            if (!DATA.pages || DATA.pages.length === 0) {
                hideLoader();
                showToast('No page images available for this brochure.', 4000);
                return;
            }

            const pages = DATA.pages;
            totalPages  = pages.length;

            setProgress(30, 'Loading images…');

            // For image mode, create <img> elements — StPageFlip supports loadFromImages()
            // But using HTML elements gives us more control (lazy loading via loading="lazy")
            const { width, height } = getFlipDimensions();
            const imageEls = [];
            let   loaded   = 0;

            for (let i = 0; i < pages.length; i++) {
                const img = document.createElement('img');
                img.src     = pages[i];
                img.loading = i < 4 ? 'eager' : 'lazy';
                img.alt     = 'Page ' + (i + 1);
                img.style.cssText = 'display:block;width:' + width + 'px;height:' + height + 'px;object-fit:cover;';
                img.classList.add('page-canvas');

                img.addEventListener('load', () => {
                    loaded++;
                    const pct = 30 + Math.round((loaded / Math.min(4, pages.length)) * 65);
                    setProgress(Math.min(pct, 95), 'Loading page ' + loaded + '…');
                });

                imageEls.push(img);
            }

            // Wait for first 4 images then initialize
            const eager = imageEls.slice(0, Math.min(4, pages.length));
            const promises = eager.map(img =>
                img.complete
                    ? Promise.resolve()
                    : new Promise(res => { img.onload = res; img.onerror = res; })
            );

            Promise.all(promises).then(() => {
                setProgress(95, 'Initializing flipbook…');

                initPageFlipInstance(imageEls);

                setProgress(100, 'Ready!');
                updateIndicator(currentPage, totalPages);

                if (currentPage > 1 && pageFlip) {
                    pageFlip.turnToPage(currentPage - 1);
                }

                hideLoader();
            });
        }

        // ── ZOOM ───────────────────────────────────────────────────────

        function openZoom() {
            if (!pageFlip) return;

            const idx  = pageFlip.getCurrentPageIndex(); // 0-based
            const page = document.querySelectorAll('.page-canvas')[idx];

            if (!page) return;

            if (page.tagName === 'CANVAS') {
                zoomCanvas.width  = page.width;
                zoomCanvas.height = page.height;
                zoomCanvas.getContext('2d').drawImage(page, 0, 0);
            } else if (page.tagName === 'IMG') {
                zoomCanvas.width  = page.naturalWidth  || page.width;
                zoomCanvas.height = page.naturalHeight || page.height;
                zoomCanvas.getContext('2d').drawImage(page, 0, 0, zoomCanvas.width, zoomCanvas.height);
            } else {
                return;
            }

            zoomOverlay.classList.add('active');
        }

        function closeZoom() {
            zoomOverlay.classList.remove('active');
        }

        zoomOverlay.addEventListener('click', closeZoom);
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeZoom(); });
        btnZoom.addEventListener('click', openZoom);

        // ── CONTROLS ──────────────────────────────────────────────────

        btnFirst.addEventListener('click', () => {
            if (pageFlip) { pageFlip.flip(0); }
        });

        btnPrev.addEventListener('click', () => {
            if (pageFlip) { pageFlip.flipPrev(); }
        });

        btnNext.addEventListener('click', () => {
            if (pageFlip) { pageFlip.flipNext(); }
        });

        btnLast.addEventListener('click', () => {
            if (pageFlip) { pageFlip.flip(totalPages - 1); }
        });

        // Keyboard navigation
        document.addEventListener('keydown', function (e) {
            if (zoomOverlay.classList.contains('active')) return;
            if (!pageFlip) return;

            if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                e.preventDefault();
                pageFlip.flipNext();
            } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                e.preventDefault();
                pageFlip.flipPrev();
            } else if (e.key === 'Home') {
                e.preventDefault();
                pageFlip.flip(0);
            } else if (e.key === 'End') {
                e.preventDefault();
                pageFlip.flip(totalPages - 1);
            } else if (e.key === 'z' || e.key === 'Z') {
                openZoom();
            } else if ((e.key === 'd' || e.key === 'D') && DATA.pdfUrl) {
                // Trigger download via hidden link
                const dl = document.createElement('a');
                dl.href     = DATA.pdfUrl;
                dl.download = DATA.slug + '.pdf';
                dl.click();
            }
        });

        // ── SOUND TOGGLE ──────────────────────────────────────────────

        btnSound.addEventListener('click', () => {
            soundEnabled = !soundEnabled;
            soundIconOn.style.display  = soundEnabled ? '' : 'none';
            soundIconOff.style.display = soundEnabled ? 'none' : '';
            showToast(soundEnabled ? 'Sound on' : 'Sound off', 1500);
        });

        // ── ANALYTICS (non-blocking, best-effort) ─────────────────────

        function trackPageView(page) {
            // Fires a custom event — hook this up to GA4 / your analytics
            try {
                if (window.gtag) {
                    window.gtag('event', 'brochure_page_view', {
                        brochure_slug: DATA.slug,
                        page_number:   page,
                    });
                }
            } catch (_) {}
        }

        function trackOpen() {
            try {
                if (window.gtag) {
                    window.gtag('event', 'brochure_open', { brochure_slug: DATA.slug });
                }
            } catch (_) {}
        }

        // ── UTILITIES ─────────────────────────────────────────────────

        function debounce(fn, wait) {
            let t;
            return function (...args) {
                clearTimeout(t);
                t = setTimeout(() => fn.apply(this, args), wait);
            };
        }

        // ── BOOT ──────────────────────────────────────────────────────

        /**
         * Wait for both StPageFlip and (if PDF mode) PDF.js to be ready,
         * then initialise.
         */
        function waitForLibs(callback, attempts = 0) {
            const pdfReady  = DATA.mode === 'images' || (typeof pdfjsLib !== 'undefined');
            const flipReady = typeof St !== 'undefined' && typeof St.PageFlip !== 'undefined';

            if (pdfReady && flipReady) {
                callback();
            } else if (attempts < 100) {
                // Retry every 100ms (max 10s)
                setTimeout(() => waitForLibs(callback, attempts + 1), 100);
            } else {
                setProgress(100, 'Failed to load viewer libraries.');
            }
        }

        window.addEventListener('load', function () {
            trackOpen();

            waitForLibs(function () {
                if (DATA.mode === 'pdf') {
                    initPdfMode();
                } else {
                    initImageMode();
                }
            });
        });

    })();
    </script>

</body>
</html>
