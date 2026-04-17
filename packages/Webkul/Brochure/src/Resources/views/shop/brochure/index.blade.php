@push('meta')
    <meta
        name="description"
        content="{{ trans('brochure::app.shop.index.meta-description') }}"
    />
@endpush



<x-shop::layouts>

    @push('styles')
        <style>
            .broucher-card{
                transform-origin: left;
                transition: transform 0.3s ease;
                transform-style: preserve-3d;
            }
            .broucher-card:hover{
                transform: translateY(-10px);
            }
            .book-cover {
                background: linear-gradient(to right, #ff7e5f, #feb47b);
            }
            .broucher-card .hover-button{
                opacity: 0;
                visibility: hidden;
            }
            .broucher-card:hover .hover-button {
                opacity: 1;
                visibility: visible;
            }
            @media screen and (max-width: 991px){
                .broucher-card .hover-button{
                    opacity: 1;
                    visibility: visible;
                }
            }
        </style>
    @endpush

    <x-slot:title>
        {{ $metaTitle ?? trans('brochure::app.shop.index.title') }}
    </x-slot>

    {{-- ── Hero Section ──────────────────────────────────────────── --}}
    <div
        class="bg-[#EDEDED] container px-[60px] max-lg:px-8 max-sm:px-4 py-[80px] md:py-[150px] text-white text-center relative z-0 before:absolute before:inset-0 before:z-[1] before:bg-black/50 before:content-['']">
        <img src="{{ bagisto_asset('images/search-page-banner.jpg') }}" alt="Search Icon"
            class="absolute inset-0 h-full w-full object-cover object-center" />
        @if (request()->has('image-search'))
            @include('shop::search.images.results')
        @endif
        <div class="flex items-center text-center justify-center relative z-[2]">
            <h1 class="text-[24px] xl:text-5xl font-normal font-secondary">
                @lang('brochure::app.shop.index.heading')
            </h1>
        </div>
    </div>

    {{-- ── Listing Section ───────────────────────────────────────── --}}
    <section class="broucher-section">
        <div class="container">

            @if ($brochures->isEmpty())

                {{-- Premium empty state --}}
                <div class="bc-empty text-center">
                    <div class="bc-empty-icon-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <p class="bc-empty-title">@lang('brochure::app.shop.index.no-brochures')</p>
                    <p class="bc-empty-text">Check back soon — new catalogs are on their way.</p>
                </div>

            @else

                <div class="mb-4">
                    <span class="text-lg">{{ $brochures->count() }} {{ $brochures->count() === 1 ? 'Catalog' : 'Catalogs' }} Available</span>
                </div>

                <div class="grid-boxes">
                    @foreach ($brochures as $index => $brochure)

                        <div class="relative">
                            <article class="broucher-card shadow rounded-md overflow-hidden h-full">

                                {{-- ── Book face (cover) ─── --}}
                                <a class="relative" href="{{ route('shop.brochure.view', $brochure->slug) }}" class="relative" aria-label="{{ $brochure->title }}">

                                    {{-- Real cover image (when uploaded) --}}
                                    @if ($brochure->cover_image_url)
                                        <img
                                            class="aspect-[9/11] object-cover"
                                            src="{{ $brochure->cover_image_url }}"
                                            alt="{{ $brochure->title }}"
                                            loading="lazy"
                                        />
                                    @endif

                                    {{-- Spine (always on top of cover image) --}}
                                    <div class="bc-spine" aria-hidden="true"></div>
                                    <div class="bc-spine-crease" aria-hidden="true"></div>

                                    {{-- Concentric circle pattern — hidden when cover image present --}}
                                    @if (! $brochure->cover_image_url)
                                    <div class="bc-cover-pattern" aria-hidden="true"></div>

                                    {{-- Centre icon — shown only without cover image --}}
                                    <div class="aspect-[9/11] object-cover book-cover content-center text-center" aria-hidden="true">
                                        <svg class="w-40 h-40 mx-auto" viewBox="0 0 64 64" fill="none" stroke="rgba(255,255,255,0.8)" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
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
                                    @endif

                                    {{-- Cover footer --}}
                                    <div class="hover-button absolute inset-0 content-center text-center transition-all duration-300">
                                        
                                        <div class="" aria-hidden="true">
                                            <span class="uppercase inline-flex items-center gap-1 md:gap-2.5 text-[#902129] hover:text-white border border-[#902129] px-1.5 md:px-4 py-2.5 bg-white rounded hover:bg-[#902129] transition-all duration-300 text-xs md:text-base">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                                                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                                                </svg>
                                                Open Flipbook
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Gloss overlay --}}
                                    <div class="bc-cover-gloss" aria-hidden="true"></div>

                                    {{-- Type badge --}}
                                    <span class="text-xs md:text-sm text-gray-800 absolute top-3 right-3 bg-red-500 py-1 md:py-1.5 px-2 md:px-3 rounded-md font-semibold text-white">
                                        {{ $brochure->type === 'pdf' ? 'PDF' : 'Catalog' }}
                                    </span>

                                </a>

                                {{-- ── Card info ─── --}}
                                <div class="p-2.5 md:p-4">
                                    <h3 class="text-sm md:text-lg mb-0">{{ $brochure->title }}</h3>
                                    <div class="text-xs md:text-base">
                                        <a href="{{ route('shop.brochure.view', $brochure->slug) }}" class="inline-flex items-center gap-1">
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
