@if ($uspItems->isNotEmpty())
    {{-- <section class="mt-7 rounded-[28px] border border-[#DCCFD1] bg-white p-4 shadow-[0_18px_45px_rgba(15,29,113,0.08)] sm:p-5"> --}}
    {{-- <div class="flex items-start justify-between gap-4 border-b border-[#EDEDED] pb-3">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#902129]">
                    Featured Highlights
                </p>

                <h2 class="mt-1 font-secondary text-lg font-normal uppercase text-[#0F1D71] sm:text-xl">
                    Built For Performance
                </h2>
            </div>
        </div> --}}

    {{-- <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
        @foreach ($uspItems as $usp)
            <article
                class="flex items-center gap-3 rounded-2xl border border-[#EEE6E7] bg-[#FCFAFA] p-4 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_14px_30px_rgba(144,33,41,0.08)] hover:shadow-lg group">
                <div
                    class="flex h-12 w-12 shrink-0 items-center justify-center
                    rounded-xl
                    bg-gradient-to-br from-[#1A2A8F] via-[#902129] to-[#C43A3A]
                    text-white
                    shadow-[0_12px_30px_rgba(144,33,41,0.25)]
                    ring-2 ring-white/60">
                    @if ($usp['icon'] === 1)
                        <svg width="20" height="20" viewBox="0 0 256 256" fill="none"
                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path
                                d="M84.27,171.73l-55.09-20.3a7.92,7.92,0,0,1,0-14.86l55.09-20.3,20.3-55.09a7.92,7.92,0,0,1,14.86,0l20.3,55.09,55.09,20.3a7.92,7.92,0,0,1,0,14.86l-55.09,20.3-20.3,55.09a7.92,7.92,0,0,1-14.86,0Z"
                                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="12">
                            </path>
                            <line x1="176" y1="16" x2="176" y2="64" stroke="currentColor"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="12"></line>
                            <line x1="152" y1="40" x2="200" y2="40" stroke="currentColor"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="12"></line>
                        </svg>
                    @elseif ($usp['icon'] === 2)
                        <svg width="20" height="20" viewBox="0 0 256 256" fill="none"
                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <circle cx="128" cy="128" r="96" stroke="currentColor" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="12"></circle>
                            <polyline points="128 72 128 128 184 128" stroke="currentColor" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="12"></polyline>
                        </svg>
                    @elseif ($usp['icon'] === 3)
                        <svg width="20" height="20" viewBox="0 0 256 256" fill="none"
                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <line x1="80" y1="40" x2="80" y2="88" stroke="currentColor"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="12"></line>
                            <line x1="56" y1="64" x2="104" y2="64" stroke="currentColor"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="12"></line>
                            <line x1="144" y1="80" x2="176" y2="112" stroke="currentColor"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="12"></line>
                            <rect x="21.49" y="105.37" width="213.02" height="45.25" rx="8"
                                transform="translate(-53.02 128) rotate(-45)" stroke="currentColor"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="12"></rect>
                        </svg>
                    @else
                        <svg width="20" height="20" viewBox="0 0 256 256" fill="none"
                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path
                                d="M54.46,201.54c-9.2-9.2-3.1-28.53-7.78-39.85C41.82,150,24,140.5,24,128s17.82-22,22.68-33.69C51.36,83,45.26,63.66,54.46,54.46S83,51.36,94.31,46.68C106.05,41.82,115.5,24,128,24S150,41.82,161.69,46.68c11.32,4.68,30.65-1.42,39.85,7.78s3.1,28.53,7.78,39.85C214.18,106.05,232,115.5,232,128S214.18,150,209.32,161.69c-4.68,11.32,1.42,30.65-7.78,39.85s-28.53,3.1-39.85,7.78C150,214.18,140.5,232,128,232s-22-17.82-33.69-22.68C83,204.64,63.66,210.74,54.46,201.54Z"
                                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="12">
                            </path>
                            <polyline points="88 136 112 160 168 104" stroke="currentColor" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="12"></polyline>
                        </svg>
                    @endif
                </div>

                <div class="min-w-0">
                    <p class="text-sm font-medium leading-6 text-[#211A28] sm:text-[15px]">
                        {!! nl2br(e($usp['value'])) !!}
                    </p>
                </div>
            </article>
        @endforeach
    </div> --}}
    <div class="mt-4 border-t border-b border-gray-200 py-3">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-3 gap-x-6 text-sm text-gray-700">

            @foreach ($uspItems as $usp)
                <div class="flex items-center gap-3">

                    <!-- ICON (PNG) -->
                    <div class="w-8 h-8 shrink-0 flex items-center justify-center">
                        @if ($usp['icon'] === 1)
                            <img src="{{ bagisto_asset('images/preknocked.svg') }}" alt="Pre Knocked"
                                class="w-full h-full object-contain">
                        @elseif ($usp['icon'] === 2)
                            <img src="{{ bagisto_asset('images/warranty.svg') }}" alt="Warranty"
                                class="w-full h-full object-contain">
                        @elseif ($usp['icon'] === 3)
                            <img src="{{ bagisto_asset('images/lightweight.svg') }}" alt="Lightweight"
                                class="w-full h-full object-contain">
                        @else
                            <img src="{{ bagisto_asset('images/rocket.svg') }}" alt="Performance"
                                class="w-full h-full object-contain">
                        @endif
                    </div>

                    <!-- TEXT -->
                    <span class="text-gray-800 font-medium leading-tight">
                        {{ $usp['value'] }}
                    </span>

                </div>
            @endforeach

        </div>

    </div>
    {{-- </section> --}}
@endif
