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

    {{-- PDF.js (used for PDF mode rendering) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js" defer></script>

    {{-- StPageFlip (open-source flipbook engine, no jQuery) --}}
    <script src="https://unpkg.com/page-flip@2.0.7/dist/js/page-flip.browser.js" defer></script>

    {{-- Page flip sound (subtle, royalty-free) --}}
    <style>
        /* ================================================================
         * BROCHURE FLIPBOOK VIEWER — Full-page immersive layout
         * ================================================================ */

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            background: #0b0b0b;
            font-family: system-ui, -apple-system, sans-serif;
            overflow: hidden;
        }

        /* ---------- Top navigation bar ---------- */
        #brochure-topbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.25rem;
            height: 52px;
            background: rgba(11, 11, 11, 0.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }

        #brochure-topbar .nav-title {
            color: #fff;
            font-size: 0.95rem;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 50%;
        }

        #brochure-topbar .nav-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-btn {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.75rem;
            border: 1px solid rgba(255,255,255,0.15);
            background: transparent;
            color: rgba(255,255,255,0.8);
            font-size: 0.78rem;
            font-weight: 500;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.2s, color 0.2s, border-color 0.2s;
            text-decoration: none;
        }

        .nav-btn:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border-color: rgba(255,255,255,0.3);
        }

        .nav-btn svg { flex-shrink: 0; }

        /* ---------- Main viewer area ---------- */
        #brochure-stage {
            position: fixed;
            inset: 52px 0 64px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0b0b0b;
            overflow: hidden;
        }

        /* ---------- Flipbook container (StPageFlip mounts here) ---------- */
        #flipbook-container {
            position: relative;
            /* Dimensions are set dynamically via JS based on viewport */
        }

        /* StPageFlip page style */
        .page-canvas {
            display: block;
            background: #fff;
        }

        /* Realistic page shadow overlay injected by StPageFlip */
        .page-shadow { filter: drop-shadow(0 8px 24px rgba(0,0,0,0.6)); }

        /* ---------- Loader ---------- */
        #brochure-loader {
            position: fixed;
            inset: 0;
            z-index: 200;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1.25rem;
            background: #0b0b0b;
        }

        .loader-book {
            width: 60px;
            height: 80px;
            position: relative;
        }

        .loader-page {
            position: absolute;
            top: 0;
            right: 0;
            width: 50%;
            height: 100%;
            background: #333;
            border-radius: 0 4px 4px 0;
            transform-origin: left center;
            animation: flipPage 1.2s ease-in-out infinite alternate;
        }

        .loader-spine {
            position: absolute;
            top: 0;
            left: 0;
            width: 50%;
            height: 100%;
            background: #444;
            border-radius: 4px 0 0 4px;
        }

        @keyframes flipPage {
            0%   { transform: rotateY(0deg); }
            100% { transform: rotateY(-160deg); }
        }

        .loader-text {
            color: rgba(255,255,255,0.6);
            font-size: 0.85rem;
            letter-spacing: 0.05em;
        }

        .loader-progress {
            width: 200px;
            height: 3px;
            background: rgba(255,255,255,0.1);
            border-radius: 2px;
            overflow: hidden;
        }

        .loader-progress-bar {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, #6366f1, #8b5cf6);
            border-radius: 2px;
            transition: width 0.3s ease;
        }

        /* ---------- Bottom controls bar ---------- */
        #brochure-controls {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 100;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            background: rgba(11, 11, 11, 0.92);
            backdrop-filter: blur(12px);
            border-top: 1px solid rgba(255,255,255,0.07);
        }

        .ctrl-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border: 1px solid rgba(255,255,255,0.15);
            background: transparent;
            color: rgba(255,255,255,0.8);
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }

        .ctrl-btn:hover:not(:disabled) {
            background: rgba(255,255,255,0.12);
            color: #fff;
        }

        .ctrl-btn:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }

        #page-indicator {
            min-width: 100px;
            text-align: center;
            color: rgba(255,255,255,0.7);
            font-size: 0.82rem;
            font-variant-numeric: tabular-nums;
        }

        /* ---------- Zoom overlay ---------- */
        #zoom-overlay {
            position: fixed;
            inset: 0;
            z-index: 300;
            background: rgba(0,0,0,0.92);
            display: none;
            align-items: center;
            justify-content: center;
            cursor: zoom-out;
        }

        #zoom-overlay.active { display: flex; }

        #zoom-canvas {
            max-width: 90vw;
            max-height: 90vh;
            object-fit: contain;
            border-radius: 4px;
            box-shadow: 0 32px 64px rgba(0,0,0,0.8);
        }

        /* ---------- Toast notification ---------- */
        #brochure-toast {
            position: fixed;
            bottom: 80px;
            left: 50%;
            transform: translateX(-50%) translateY(20px);
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(8px);
            color: #fff;
            padding: 0.5rem 1.25rem;
            border-radius: 20px;
            font-size: 0.8rem;
            opacity: 0;
            transition: opacity 0.3s, transform 0.3s;
            pointer-events: none;
            white-space: nowrap;
            z-index: 150;
        }

        #brochure-toast.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        /* ---------- Responsive ---------- */
        @media (max-width: 768px) {
            #brochure-topbar .nav-btn span { display: none; }
            #brochure-controls { gap: 0.6rem; }
        }
    </style>
</head>
<body>

    {{-- PHP data passed to JavaScript --}}
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

    {{-- Loader --}}
    <div id="brochure-loader">
        <div class="loader-book">
            <div class="loader-spine"></div>
            <div class="loader-page"></div>
        </div>
        <div class="loader-text" id="loader-text">Loading brochure…</div>
        <div class="loader-progress">
            <div class="loader-progress-bar" id="loader-progress-bar"></div>
        </div>
    </div>

    {{-- Top Navigation --}}
    <header id="brochure-topbar">
        <span class="nav-title">{{ $brochure->title }}</span>
        <div class="nav-actions">
            {{-- Zoom button --}}
            <button class="nav-btn" id="btn-zoom" title="Zoom current page" aria-label="Zoom">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35M11 8v6M8 11h6"/>
                </svg>
                <span>Zoom</span>
            </button>

            {{-- Sound toggle --}}
            <button class="nav-btn" id="btn-sound" title="Toggle flip sound" aria-label="Sound">
                <svg id="sound-icon-on" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/>
                </svg>
                <svg id="sound-icon-off" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none">
                    <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><line x1="23" y1="9" x2="17" y2="15"/><line x1="17" y1="9" x2="23" y2="15"/>
                </svg>
                <span>Sound</span>
            </button>

            {{-- Back to listing --}}
            <a href="{{ route('shop.brochure.index') }}" class="nav-btn" aria-label="All Brochures">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m15 18-6-6 6-6"/>
                </svg>
                <span>All Brochures</span>
            </a>
        </div>
    </header>

    {{-- Flipbook Stage --}}
    <main id="brochure-stage">
        <div id="flipbook-container"></div>
    </main>

    {{-- Bottom Controls --}}
    <nav id="brochure-controls" aria-label="Page navigation">
        {{-- First page --}}
        <button class="ctrl-btn" id="btn-first" title="First page" aria-label="First page">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="11 17 6 12 11 7"/><polyline points="18 17 13 12 18 7"/>
            </svg>
        </button>

        {{-- Previous page --}}
        <button class="ctrl-btn" id="btn-prev" title="Previous page" aria-label="Previous page">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
        </button>

        {{-- Page indicator --}}
        <span id="page-indicator" aria-live="polite">— / —</span>

        {{-- Next page --}}
        <button class="ctrl-btn" id="btn-next" title="Next page" aria-label="Next page">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9 18 15 12 9 6"/>
            </svg>
        </button>

        {{-- Last page --}}
        <button class="ctrl-btn" id="btn-last" title="Last page" aria-label="Last page">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="13 17 18 12 13 7"/><polyline points="6 17 11 12 6 7"/>
            </svg>
        </button>
    </nav>

    {{-- Zoom overlay --}}
    <div id="zoom-overlay" role="dialog" aria-label="Zoom view">
        <canvas id="zoom-canvas"></canvas>
    </div>

    {{-- Toast --}}
    <div id="brochure-toast" role="status" aria-live="polite"></div>

    {{-- Hidden audio for page flip sound --}}
    <audio id="flip-sound" preload="auto" style="display:none">
        {{-- Base64-encoded minimal page-flip click sound (PCM WAV, ~1KB) --}}
        <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAA..." type="audio/wav" />
    </audio>

    {{-- ================================================================
         BROCHURE FLIPBOOK ENGINE
         ================================================================ --}}
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
            const stageH = window.innerHeight - 52 - 64; // topbar + controls
            const ratio  = 1.414; // A4 aspect ratio

            if (isMobile()) {
                // Single-page mode on mobile — fill most of the stage
                const w = Math.min(stageW * 0.92, 420);
                return { width: Math.round(w), height: Math.round(w * ratio) };
            }

            // Desktop: two-page spread
            const maxW = Math.min(stageW * 0.90, 1200);
            const maxH = stageH * 0.92;
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
                pdfDoc     = await pdfjsLib.getDocument({ url: DATA.pdfUrl, cMapUrl: 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/cmaps/', cMapPacked: true }).promise;
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

                // Size all canvases from page 1 viewport
                const firstPage = await pdfDoc.getPage(1);
                const scale     = isMobile() ? 1.2 : 2.0;
                const viewport  = firstPage.getViewport({ scale });

                pages.forEach(c => {
                    c.width  = viewport.width;
                    c.height = viewport.height;
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
                    const pageNum = i + 1;
                    const page    = await pdfDoc.getPage(pageNum);
                    const canvas  = document.getElementById('pdf-page-' + pageNum);

                    if (!canvas) continue;

                    const scale    = isMobile() ? 1.2 : 2.0;
                    const viewport = page.getViewport({ scale });

                    canvas.width  = viewport.width;
                    canvas.height = viewport.height;

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
