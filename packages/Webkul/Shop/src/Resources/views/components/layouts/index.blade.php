@props([
    'hasHeader' => true,
    'hasFeature' => true,
    'hasFooter' => true,
])

<!DOCTYPE html>

<html lang="{{ app()->getLocale() }}" dir="{{ core()->getCurrentLocale()->direction }}">

<head>

    {!! view_render_event('bagisto.shop.layout.head.before') !!}

    <title>{{ $title ?? '' }}</title>

    <meta charset="UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="content-language" content="{{ app()->getLocale() }}">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="base-url" content="{{ url()->to('/') }}">
    <meta name="currency" content="{{ core()->getCurrentCurrency()->toJson() }}">

    <!-- Canonical Tag -->
    <link rel="canonical" href="{{ request()->url() }}">

    @stack('meta')

    <!-- Website Schema -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org/",
            "@type": "WebSite",
            "name": "{{ config('app.name') }}",
            "url": "{{ url('/') }}",
            "potentialAction": {
                "@type": "SearchAction",
                "target": "{{ url('/search') }}?q={search_term_string}",
                "query-input": "required name=search_term_string"
            }
        }
    </script>

    <!-- Store Schema -->
    @if (request()->is('/') || request()->routeIs('shop.home.index'))
        <script type="application/ld+json">
            {
                "@context": "https://schema.org",
                "@type": "Store",
                "name": "{{ config('app.name') }}",
                "image": "{{ core()->getCurrentChannel()->logo_url }}",
                "url": "{{ url('/') }}",
                "telephone": "9311048371",
                "priceRange": "₹2000-₹60000",
                "address": {
                    "@type": "PostalAddress",
                    "streetAddress": "AARYA ADRIJA SPORTS PVT. LTD. Kila Parikshit Garh Rd, Makshoodpur ganvadi, Abdullapur",
                    "addressLocality": "Meerut, Uttar Pradesh",
                    "postalCode": "250004",
                    "addressCountry": "IN"
                },
                "geo": {
                    "@type": "GeoCoordinates",
                    "latitude": 28.9884023,
                    "longitude": 77.77076
                },
                "openingHoursSpecification": {
                    "@type": "OpeningHoursSpecification",
                    "dayOfWeek": [
                    "Monday",
                    "Tuesday",
                    "Wednesday",
                    "Thursday",
                    "Friday",
                    "Saturday",
                    "Sunday"
                    ],
                    "opens": "09:30",
                    "closes": "19:00"
                }
            }
        </script>
    @endif

    <link rel="icon" sizes="16x16"
        href="{{ core()->getCurrentChannel()->favicon_url ?? bagisto_asset('images/favicon.ico') }}" />

    @bagistoVite(['src/Resources/assets/css/app.css', 'src/Resources/assets/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin />

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />

    <link rel="preload" as="style"
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=DM+Serif+Display&display=swap" />

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=DM+Serif+Display&display=swap" />

    @stack('styles')

    <style>
        {!! core()->getConfigData('general.content.custom_scripts.custom_css') !!}
    </style>

    @if (core()->getConfigData('general.content.speculation_rules.enabled'))
        <script type="speculationrules">
                @json(core()->getSpeculationRules(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            </script>
    @endif

    {!! view_render_event('bagisto.shop.layout.head.after') !!}

    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start':

                    new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],

                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =

                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);

        })(window, document, 'script', 'dataLayer', 'GTM-KCRRXZZ8');
    </script>
    <!-- End Google Tag Manager -->

</head>

<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KCRRXZZ8" height="0" width="0"
            style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    {!! view_render_event('bagisto.shop.layout.body.before') !!}

    <a href="#main" class="skip-to-main-content-link">
        Skip to main content
    </a>

    <div id="app">
        <!-- Flash Message Blade Component -->
        <x-shop::flash-group />

        <!-- Confirm Modal Blade Component -->
        <x-shop::modal.confirm />

        <!-- Page Header Blade Component -->
        @if ($hasHeader)
            <x-shop::layouts.header />
        @endif

        @if (core()->getConfigData('general.gdpr.settings.enabled') && core()->getConfigData('general.gdpr.cookie.enabled'))
            <x-shop::layouts.cookie />
        @endif

        {!! view_render_event('bagisto.shop.layout.content.before') !!}

        <!-- Page Content Blade Component -->
        <main id="main" class="bg-white">
            {{ $slot }}
        </main>

        {!! view_render_event('bagisto.shop.layout.content.after') !!}


        <!-- Page Services Blade Component -->
        @if ($hasFeature)
            <x-shop::layouts.services />
        @endif

        <!-- Page Footer Blade Component -->
        @if ($hasFooter)
            <x-shop::layouts.footer />
        @endif
    </div>

    {!! view_render_event('bagisto.shop.layout.body.after') !!}

    @stack('scripts')

    {!! view_render_event('bagisto.shop.layout.vue-app-mount.before') !!}
    <script>
        /**
         * Load event, the purpose of using the event is to mount the application
         * after all of our `Vue` components which is present in blade file have
         * been registered in the app. No matter what `app.mount()` should be
         * called in the last.
         */
        window.addEventListener("load", function(event) {
            app.mount("#app");
        });
    </script>

    {!! view_render_event('bagisto.shop.layout.vue-app-mount.after') !!}

    <script type="text/javascript">
        {!! core()->getConfigData('general.content.custom_scripts.custom_javascript') !!}
    </script>
</body>

</html>
