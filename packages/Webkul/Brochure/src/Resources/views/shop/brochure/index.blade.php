<x-shop::layouts>

    <x-slot:title>
        {{ $metaTitle ?? trans('brochure::app.shop.index.title') }}
    </x-slot>

    <x-slot:seoMetaTags>
        <meta name="description" content="{{ trans('brochure::app.shop.index.meta-description') }}" />
    </x-slot>

    {{-- Brochure Listing Page --}}
    <div class="container mx-auto px-4 py-10 max-sm:px-4">

        {{-- Page Header --}}
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white max-sm:text-2xl">
                @lang('brochure::app.shop.index.heading')
            </h1>
            <p class="mt-2 text-base text-gray-500 dark:text-gray-400">
                @lang('brochure::app.shop.index.subheading')
            </p>
        </div>

        @if ($brochures->isEmpty())
            {{-- Empty state --}}
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <svg class="mb-4 h-16 w-16 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <p class="text-lg font-medium text-gray-500 dark:text-gray-400">
                    @lang('brochure::app.shop.index.no-brochures')
                </p>
            </div>
        @else
            {{-- Brochure Grid --}}
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($brochures as $brochure)
                    <div class="group flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-gray-700 dark:bg-gray-800">

                        {{-- Cover Image / Placeholder --}}
                        <div class="relative flex h-48 items-center justify-center overflow-hidden bg-gradient-to-br from-gray-800 to-gray-900">
                            {{-- Book icon placeholder --}}
                            <div class="flex flex-col items-center gap-2 text-white/70">
                                <svg class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <span class="text-xs uppercase tracking-widest opacity-60">
                                    {{ strtoupper($brochure->type) }}
                                </span>
                            </div>

                            {{-- Hover overlay --}}
                            <div class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                                <a
                                    href="{{ route('shop.brochure.view', $brochure->slug) }}"
                                    class="rounded-lg bg-white px-4 py-2 text-sm font-semibold text-gray-900 transition-transform duration-200 hover:scale-105"
                                >
                                    @lang('brochure::app.shop.index.view-btn')
                                </a>
                            </div>
                        </div>

                        {{-- Card Body --}}
                        <div class="flex flex-1 flex-col gap-3 p-4">
                            <h3 class="line-clamp-2 text-base font-semibold text-gray-800 dark:text-white">
                                {{ $brochure->title }}
                            </h3>

                            @if ($brochure->meta_description)
                                <p class="line-clamp-2 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $brochure->meta_description }}
                                </p>
                            @endif

                            <div class="mt-auto">
                                <a
                                    href="{{ route('shop.brochure.view', $brochure->slug) }}"
                                    class="block w-full rounded-lg bg-gray-900 px-4 py-2.5 text-center text-sm font-semibold text-white transition-colors duration-200 hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600"
                                >
                                    @lang('brochure::app.shop.index.view-btn')
                                </a>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif

    </div>

</x-shop::layouts>
