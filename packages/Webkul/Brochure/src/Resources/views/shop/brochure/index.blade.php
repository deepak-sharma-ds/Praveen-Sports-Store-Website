@push('meta')
    <meta
        name="description"
        content="{{ trans('brochure::app.shop.index.meta-description') }}"
    />
@endpush

<x-shop::layouts>

    <x-slot:title>
        {{ $metaTitle ?? trans('brochure::app.shop.index.title') }}
    </x-slot>

    {{-- ============================================================
         Inject CSS into <head> via @stack('styles') — avoids the
         Vue template compiler warning that strips <style> tags
         placed inside the slot content.
         ============================================================ --}}
    @push('styles')
    <style>
        /* ── Brand tokens ────────────────────────────────── */
        :root {
            --bc-navy:        #060C3B;
            --bc-navy-mid:    #0d1a5e;
            --bc-navy-light:  #1a2a8a;
            --bc-maroon:      #902129;
            --bc-maroon-dark: #6b0e14;
            --bc-maroon-btn:  #a82530;
            --bc-cream:       #F6F2EB;
            --bc-light:       #EDEDED;
        }

        /* ── Hero banner ─────────────────────────────────── */
        .bc-hero {
            position: relative;
            background: var(--bc-navy);
            padding: 4rem 1.5rem 3.25rem;
            text-align: center;
            overflow: hidden;
        }

        /* Radial glow behind content */
        .bc-hero::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 700px;
            height: 220px;
            background: radial-gradient(ellipse, rgba(144,33,41,0.22) 0%, transparent 68%);
            pointer-events: none;
        }

        /* Decorative large book outlines (subtle bg element) */
        .bc-hero::after {
            content: '';
            position: absolute;
            bottom: -30px;
            right: -40px;
            width: 220px;
            height: 280px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 80 100' fill='none' stroke='rgba(255,255,255,0.04)' stroke-width='1.5'%3E%3Cpath d='M4 8h28a4 4 0 0 1 4 4v72a4 4 0 0 1-4 4H4V8z'/%3E%3Cpath d='M36 12h28a4 4 0 0 1 4 4v68a4 4 0 0 1-4 4H36V12z'/%3E%3Cline x1='36' y1='14' x2='36' y2='86' stroke-width='2'/%3E%3C/svg%3E") no-repeat center/contain;
            pointer-events: none;
            opacity: 1;
        }

        .bc-hero-eyebrow {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background: rgba(144,33,41,0.18);
            border: 1px solid rgba(144,33,41,0.38);
            color: #f5b3b8;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            padding: 0.32rem 0.9rem;
            border-radius: 20px;
            margin-bottom: 1rem;
        }

        .bc-hero-eyebrow span {
            display: inline-block;
            width: 5px;
            height: 5px;
            background: var(--bc-maroon);
            border-radius: 50%;
        }

        .bc-hero-title {
            position: relative;
            font-family: 'DM Serif Display', Georgia, serif;
            font-size: clamp(2.1rem, 5vw, 3.4rem);
            font-weight: 400;
            color: #ffffff;
            line-height: 1.12;
            margin-bottom: 0.9rem;
            letter-spacing: -0.01em;
        }

        .bc-hero-subtitle {
            position: relative;
            color: rgba(255,255,255,0.5);
            font-size: 0.94rem;
            line-height: 1.7;
            max-width: 460px;
            margin: 0 auto;
        }

        /* ── Listing section ─────────────────────────────── */
        .bc-listing {
            background: #ffffff;
            padding: 3.5rem 1.5rem 5.5rem;
        }

        .bc-listing-inner {
            max-width: 1320px;
            margin: 0 auto;
        }

        /* Section label above grid */
        .bc-section-label {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 2rem;
        }

        .bc-section-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--bc-light);
        }

        .bc-section-label-text {
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #9ca3af;
            white-space: nowrap;
        }

        /* ── Book grid ───────────────────────────────────── */
        .bc-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 2rem;
        }

        /* ── Card entrance animation ─────────────────────── */
        .bc-card-wrap {
            /* opacity: 0; */
            transform: translateY(22px) scale(0.97);
            transition: opacity 0.55s cubic-bezier(0.23, 1, 0.32, 1),
                        transform 0.55s cubic-bezier(0.23, 1, 0.32, 1);
            transition-delay: var(--delay, 0s);
        }

        .bc-card-wrap.bc-visible {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        /* ── The book card ───────────────────────────────── */
        .bc-card {
            display: flex;
            flex-direction: column;
            border-radius: 2px 10px 10px 2px;
            background: #fff;
            cursor: pointer;
            /* Directional shadow simulating light from top-right */
            box-shadow:
                -5px 6px 22px rgba(6,12,59,0.13),
                -2px 3px 10px rgba(6,12,59,0.08),
                0 0 0 1px rgba(6,12,59,0.05);
            /* 3D book tilt — opens from left spine */
            transform: perspective(700px) rotateY(0deg);
            transform-origin: left center;
            transition:
                transform 0.48s cubic-bezier(0.23, 1, 0.32, 1),
                box-shadow 0.48s ease;
            overflow: hidden;
        }

        .bc-card:hover {
            transform: perspective(700px) rotateY(-12deg) translateX(5px);
            box-shadow:
                -22px 14px 52px rgba(6,12,59,0.22),
                -10px 7px 26px rgba(6,12,59,0.14),
                0 0 0 1px rgba(6,12,59,0.06);
        }

        /* ── Book cover (image area) ─────────────────────── */
        .bc-book-face {
            position: relative;
            /* Classic book aspect ratio slightly wider than A4 */
            aspect-ratio: 3 / 4;
            overflow: hidden;
            display: block;
            text-decoration: none;
            background: linear-gradient(148deg, var(--bc-navy) 0%, var(--bc-navy-mid) 55%, var(--bc-navy-light) 100%);
        }

        /* Maroon spine strip */
        .bc-spine {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 13px;
            background: linear-gradient(
                180deg,
                var(--bc-maroon-dark) 0%,
                var(--bc-maroon)      35%,
                #c0392b               55%,
                var(--bc-maroon)      78%,
                var(--bc-maroon-dark) 100%
            );
            z-index: 5;
        }

        /* Spine → cover binding shadow */
        .bc-spine-crease {
            position: absolute;
            left: 13px;
            top: 0;
            bottom: 0;
            width: 22px;
            background: linear-gradient(90deg, rgba(0,0,0,0.38) 0%, rgba(0,0,0,0.06) 65%, transparent 100%);
            z-index: 4;
            pointer-events: none;
        }

        /* Concentric circle decorative pattern */
        .bc-cover-pattern {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .bc-cover-pattern::before {
            content: '';
            position: absolute;
            top: -40%;
            right: -35%;
            width: 200%;
            height: 200%;
            border-radius: 50%;
            border: 50px solid rgba(255,255,255,0.035);
        }

        .bc-cover-pattern::after {
            content: '';
            position: absolute;
            bottom: -50%;
            left: -40%;
            width: 150%;
            height: 150%;
            border-radius: 50%;
            border: 30px solid rgba(255,255,255,0.025);
        }

        /* Center cover icon area */
        .bc-cover-icon-wrap {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -58%);
            opacity: 0.45;
            transition: opacity 0.3s ease, transform 0.4s ease;
        }

        .bc-card:hover .bc-cover-icon-wrap {
            opacity: 0.2;
            transform: translate(-50%, -62%) scale(1.05);
        }

        .bc-cover-icon-wrap svg {
            width: 64px;
            height: 64px;
        }

        /* Bottom footer area on cover */
        .bc-cover-footer {
            position: absolute;
            bottom: 0;
            left: 13px;
            right: 0;
            padding: 0.65rem 0.75rem;
            background: linear-gradient(0deg, rgba(0,0,0,0.55) 0%, transparent 100%);
            z-index: 3;
        }

        .bc-cover-brand-name {
            display: block;
            font-size: 0.52rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.4);
            margin-bottom: 0.18rem;
        }

        .bc-cover-title-text {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: rgba(255,255,255,0.88);
            line-height: 1.3;
            /* Clamp to 2 lines */
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Gloss sheen (top-left light reflection) */
        .bc-cover-gloss {
            position: absolute;
            top: 0;
            left: 13px;
            right: 0;
            height: 52%;
            background: linear-gradient(
                160deg,
                rgba(255,255,255,0.1) 0%,
                rgba(255,255,255,0.04) 40%,
                transparent 100%
            );
            pointer-events: none;
            z-index: 6;
        }

        /* Type badge (top-right) */
        .bc-type-badge {
            position: absolute;
            top: 0.7rem;
            right: 0.6rem;
            background: rgba(144,33,41,0.88);
            color: #fff;
            font-size: 0.52rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 0.22rem 0.48rem;
            border-radius: 3px;
            z-index: 7;
        }

        /* Hover reveal: CTA overlay */
        .bc-cover-hover-overlay {
            position: absolute;
            inset: 0;
            background: rgba(6,12,59,0.78);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.28s ease;
            z-index: 10;
        }

        .bc-card:hover .bc-cover-hover-overlay {
            opacity: 1;
        }

        .bc-hover-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background: var(--bc-maroon);
            color: #fff;
            font-size: 0.78rem;
            font-weight: 600;
            padding: 0.62rem 1.3rem;
            border-radius: 7px;
            text-decoration: none;
            transform: translateY(10px) scale(0.94);
            transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1),
                        background 0.2s;
        }

        .bc-card:hover .bc-hover-btn {
            transform: translateY(0) scale(1);
        }

        .bc-hover-btn:hover {
            background: #a82530;
        }

        /* ── Card info (below cover) ─────────────────────── */
        .bc-card-info {
            padding: 0.9rem 0.9rem 1rem 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
            flex: 1;
            border-top: 1px solid rgba(6,12,59,0.06);
        }

        .bc-card-title {
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--bc-navy);
            line-height: 1.38;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin: 0;
        }

        .bc-card-desc {
            font-size: 0.76rem;
            color: #6b7280;
            line-height: 1.55;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin: 0;
        }

        .bc-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 0.4rem;
        }

        .bc-card-cta {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.76rem;
            font-weight: 600;
            color: var(--bc-maroon);
            text-decoration: none;
            transition: gap 0.22s ease, color 0.2s;
        }

        .bc-card-cta:hover {
            color: var(--bc-maroon-dark);
            gap: 0.5rem;
        }

        .bc-card-cta svg {
            transition: transform 0.22s ease;
        }

        .bc-card-cta:hover svg {
            transform: translateX(2px);
        }

        /* ── Empty state ─────────────────────────────────── */
        .bc-empty {
            text-align: center;
            padding: 6rem 1.5rem;
        }

        .bc-empty-icon-wrap {
            width: 88px;
            height: 88px;
            background: var(--bc-cream);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.75rem;
        }

        .bc-empty-icon-wrap svg {
            width: 38px;
            height: 38px;
            color: var(--bc-maroon);
            opacity: 0.65;
        }

        .bc-empty-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--bc-navy);
            margin-bottom: 0.5rem;
        }

        .bc-empty-text {
            font-size: 0.9rem;
            color: #9ca3af;
            max-width: 320px;
            margin: 0 auto;
        }

        /* ── Responsive ──────────────────────────────────── */
        @media (max-width: 768px) {
            .bc-hero { padding: 3rem 1rem 2.5rem; }
            .bc-listing { padding: 2.5rem 1rem 4rem; }
            .bc-grid { grid-template-columns: repeat(2, 1fr); gap: 1.25rem; }
        }

        @media (max-width: 420px) {
            .bc-grid { grid-template-columns: 1fr; max-width: 280px; margin: 0 auto; }
        }
    </style>
    @endpush

    {{-- ── Hero Section ──────────────────────────────────────────── --}}
    <section class="bc-hero">
        <span class="bc-hero-eyebrow">
            <span></span>
            Digital Catalog
        </span>
        <h1 class="bc-hero-title">@lang('brochure::app.shop.index.heading')</h1>
        <p class="bc-hero-subtitle">@lang('brochure::app.shop.index.subheading')</p>
    </section>

    {{-- ── Listing Section ───────────────────────────────────────── --}}
    <section class="bc-listing">
        <div class="bc-listing-inner">

            @if ($brochures->isEmpty())

                {{-- Premium empty state --}}
                <div class="bc-empty">
                    <div class="bc-empty-icon-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <p class="bc-empty-title">@lang('brochure::app.shop.index.no-brochures')</p>
                    <p class="bc-empty-text">Check back soon — new catalogs are on their way.</p>
                </div>

            @else

                <div class="bc-section-label">
                    <span class="bc-section-label-text">{{ $brochures->count() }} {{ $brochures->count() === 1 ? 'Catalog' : 'Catalogs' }} Available</span>
                </div>

                <div class="bc-grid">
                    @foreach ($brochures as $index => $brochure)

                        <div
                            class="bc-card-wrap"
                            style="--delay: {{ min($index * 0.07, 0.56) }}s"
                        >
                            <article class="bc-card">

                                {{-- ── Book face (cover) ─── --}}
                                <a href="{{ route('shop.brochure.view', $brochure->slug) }}" class="bc-book-face" aria-label="{{ $brochure->title }}">

                                    {{-- Spine --}}
                                    <div class="bc-spine" aria-hidden="true"></div>
                                    <div class="bc-spine-crease" aria-hidden="true"></div>

                                    {{-- Concentric circle pattern --}}
                                    <div class="bc-cover-pattern" aria-hidden="true"></div>

                                    {{-- Centre icon — cricket / sports SVG --}}
                                    <div class="bc-cover-icon-wrap" aria-hidden="true">
                                        <svg viewBox="0 0 64 64" fill="none" stroke="rgba(255,255,255,0.8)" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                                            {{-- Open book symbol --}}
                                            <path d="M32 14 C24 12 14 12 8 14 L8 52 C14 50 24 50 32 52"/>
                                            <path d="M32 14 C40 12 50 12 56 14 L56 52 C50 50 40 50 32 52"/>
                                            <line x1="32" y1="14" x2="32" y2="52" stroke-width="1.8"/>
                                            {{-- Subtle ruled lines --}}
                                            <line x1="12" y1="24" x2="28" y2="24" opacity="0.5"/>
                                            <line x1="12" y1="30" x2="28" y2="30" opacity="0.5"/>
                                            <line x1="12" y1="36" x2="28" y2="36" opacity="0.5"/>
                                            <line x1="36" y1="24" x2="52" y2="24" opacity="0.5"/>
                                            <line x1="36" y1="30" x2="52" y2="30" opacity="0.5"/>
                                            <line x1="36" y1="36" x2="52" y2="36" opacity="0.5"/>
                                        </svg>
                                    </div>

                                    {{-- Cover footer --}}
                                    <div class="bc-cover-footer">
                                        <span class="bc-cover-brand-name">Praveen Sports</span>
                                        <span class="bc-cover-title-text">{{ $brochure->title }}</span>
                                    </div>

                                    {{-- Gloss overlay --}}
                                    <div class="bc-cover-gloss" aria-hidden="true"></div>

                                    {{-- Type badge --}}
                                    <span class="bc-type-badge">
                                        {{ $brochure->type === 'pdf' ? 'PDF' : 'Catalog' }}
                                    </span>

                                    {{-- Hover CTA overlay --}}
                                    <div class="bc-cover-hover-overlay" aria-hidden="true">
                                        <span class="bc-hover-btn">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                                                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                                            </svg>
                                            Open Flipbook
                                        </span>
                                    </div>

                                </a>

                                {{-- ── Card info ─── --}}
                                <div class="bc-card-info">
                                    <h3 class="bc-card-title">{{ $brochure->title }}</h3>

                                    @if ($brochure->meta_description)
                                        <p class="bc-card-desc">{{ $brochure->meta_description }}</p>
                                    @endif

                                    <div class="bc-card-footer">
                                        <a href="{{ route('shop.brochure.view', $brochure->slug) }}" class="bc-card-cta">
                                            @lang('brochure::app.shop.index.view-btn')
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <path d="M5 12h14M12 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>

                            </article>
                        </div>

                    @endforeach
                </div>

            @endif
        </div>
    </section>


</x-shop::layouts>

{{--
    Inject scroll-animation JS at the bottom of <body> via @stack('scripts').
    This keeps <script> tags outside Vue's component template scope,
    preventing the "Tags with side effect are ignored" Vue warning.
--}}
@push('scripts')
<script>
(function () {
    'use strict';

    function initBrochureReveal() {
        const cards = document.querySelectorAll('.bc-card-wrap');
        if (!cards.length) return;

        if ('IntersectionObserver' in window) {
            const io = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('bc-visible');
                        io.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.08, rootMargin: '0px 0px -30px 0px' });

            cards.forEach(function (card) { io.observe(card); });
        } else {
            // Fallback: reveal all immediately (no IntersectionObserver support)
            cards.forEach(function (card) { card.classList.add('bc-visible'); });
        }
    }

    // Run after DOM is ready; this script is injected near the end of the page.
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBrochureReveal);
    } else {
        initBrochureReveal();
    }
})();
</script>
@endpush
