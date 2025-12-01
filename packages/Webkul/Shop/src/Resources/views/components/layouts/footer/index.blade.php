{!! view_render_event('bagisto.shop.layout.footer.before') !!}

<!--
    The category repository is injected directly here because there is no way
    to retrieve it from the view composer, as this is an anonymous component.
-->
@inject('themeCustomizationRepository', 'Webkul\Theme\Repositories\ThemeCustomizationRepository')

<!--
    This code needs to be refactored to reduce the amount of PHP in the Blade
    template as much as possible.
-->
@php
    $channel = core()->getCurrentChannel();

    $customization = $themeCustomizationRepository->findOneWhere([
        'type' => 'footer_links',
        'status' => 1,
        'theme_code' => $channel->theme,
        'channel_id' => $channel->id,
    ]);
@endphp
@if (request()->routeIs('shop.home.index'))
    <section class="grid grid-cols-1 md:grid-cols-2 gap-2.5 text-lg">
        <div class="relative text-center z-0">
            <img src="/storage/theme/13/banner-left.png" alt=""
                class="w-full h-full object-cover aspect-square" />
            <div class="absolute top-0 left-0 bottom-0 right-0 z-[1] bg-gradient-to-t from-black to-black opacity-10">
            </div>
            <div
                class="absolute top-0 left-1/2 transform -translate-x-1/2 w-full p-4 md:p-10 text-white font-medium max-w-[540px] z-[2]">
                <h2 class="font-secondary text-2xl xl:text-4xl font-normal uppercase">Swing Smarter, Save Bigger</h2>
                <p class="mt-4">Get 15% off on all premium cricket bats. Perfect balance, powerful shots</p>
                <div class="text-center mt-5">
                    <a href="15-off-deals"
                        class="uppercase inline-flex items-center gap-2.5 text-[#902129] hover:text-white border border-[#902129] px-4 py-2.5 bg-white rounded hover:bg-[#902129] transition-all duration-300">
                        Shop Now
                    </a>
                </div>
            </div>
        </div>
        <div class="relative text-center z-0">
            <img src="/storage/theme/13/banner-right.png" alt=""
                class="w-full h-full object-cover aspect-square" />
            <div class="absolute top-0 left-0 bottom-0 right-0 z-[1] bg-gradient-to-t from-black to-black opacity-10">
            </div>
            <div
                class="absolute top-0 left-1/2 transform -translate-x-1/2 w-full p-4 md:p-10 text-white font-medium max-w-[540px] z-[2]">
                <h2 class="font-secondary text-2xl xl:text-4xl font-normal uppercase">Power. Precision. Price Drop.</h2>
                <p class="mt-4">Enjoy 20% off on select professional cricket bats. Limited-time offer — gear up before
                    it's gone!</p>
                <div class="text-center mt-5">
                    <a href="/20-off-deals"
                        class="uppercase inline-flex items-center gap-2.5 text-[#902129] hover:text-white border border-[#902129] px-4 py-2.5 bg-white rounded hover:bg-[#902129] transition-all duration-300">
                        Shop Now
                    </a>
                </div>
            </div>
        </div>
    </section>
@endif
<section>
    <div class="text-center mb-10 px-10 pt-8">
        <h2 class="font-secondary text-2xl uppercase">JOIN THE ana sport</h2>
        <p>Get exclusive 10% discount on your first purchase.</p>
        <div class="text-center mt-5">
            <a href="{{ route('shop.search.index', ['sort' => 'name-desc']) }}" class="uppercase inline-flex items-center gap-2.5 text-[15px]">
                View All
                <svg width="8" height="16" viewBox="0 0 8 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M4.93357 7.50609L4.95302 7.56225L4.95719 7.5605L4.93774 7.61665C4.92801 7.64649 3.94391 10.5369 1.37662 13.6694C0.949575 14.1883 0.490781 14.6733 0.00558099 15.1207C1.53715 14.4552 2.94075 13.5317 4.10738 12.407C6.67467 9.92157 7.65876 7.62833 7.66849 7.60466L7.68795 7.5601L7.68378 7.56149L7.66432 7.51694C7.64112 7.46341 6.65257 5.18264 4.1032 2.71464C2.9363 1.58874 1.53231 0.66524 0 4.00543e-05C0.485731 0.44794 0.944995 0.933538 1.37244 1.45334C3.9218 4.56404 4.91001 7.43761 4.93322 7.50509L4.93357 7.50609Z"
                        fill="currentColor"></path>
                </svg>
            </a>
        </div>
    </div>
    <div class="grid grid-cols-5 gap-[1px]">
        <div class="relative pt-[120%]">
            <img src="/storage/theme/13/zNfOkoGp2Wc1ZjOKgxgXGxLCZirn47STlMoFzxpa.webp"
                alt="" class="w-full h-full object-cover absolute top-0 left-0" />
        </div>
        <div class="relative pt-[120%]">
            <img src="/storage/theme/13/oMsyjpkx8GralhZGI6azqN4dWswDluSy1FFu4ISP.webp"
                alt="" class="w-full h-full object-cover absolute top-0 left-0" />
        </div>
        <div class="relative pt-[120%]">
            <img src="/storage/theme/13/FOVZJqJ5qWByjIDFmdyn33KLJLrhXMk3o6QhDKwN.webp"
                alt="" class="w-full h-full object-cover absolute top-0 left-0" />
        </div>
        <div class="relative pt-[120%]">
            <img src="/storage/theme/13/jSFtwgau6b1pb45lVY2BTvrVWFnZBsB289UtElWI.webp"
                alt="" class="w-full h-full object-cover absolute top-0 left-0" />
        </div>
        <div class="relative pt-[120%]">
            <img src="/storage/theme/13/SxcL9eRC6BE9M8jWtuWfNWtBQfLXz6kCByBi3VMF.webp"
                alt="" class="w-full h-full object-cover absolute top-0 left-0" />
        </div>
    </div>
</section>

<footer class="bg-black text-white">
    <div
        class="flex justify-between gap-y-4 lg:gap-x-6 lg:gap-y-8 py-10 px-4 lg:py-[60px] lg:px-[60px] max-1023:flex-col-reverse">
        <!-- For Desktop View -->
        <div>
            <h2 class="text-sm font-secondary" role="heading" aria-level="2">
                About Us
            </h2>
            <div class="mt-4 max-w-[400px]">
                Your one-stop destination for premium sports gear and accessories. Established with a passion for
                excellence, we are committed to delivering high-quality products...
            </div>
            <div>
                <p class="mt-3"><a class="text-white inline-flex items-center gap-2"
                        href="mailto:support@ana-sports.com"><span class="icon-telephone"><svg width="26"
                                height="24" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M25.1663 8.12611L13.1663 0.126112C13.043 0.043883 12.8982 0 12.75 0C12.6018 0 12.457 0.043883 12.3338 0.126112L0.333751 8.12611C0.230923 8.19472 0.146646 8.28768 0.0884186 8.39672C0.0301909 8.50576 -0.000182402 8.6275 8.24085e-07 8.75111V21.7511C8.24085e-07 22.2152 0.184375 22.6604 0.512564 22.9885C0.840753 23.3167 1.28587 23.5011 1.75 23.5011H23.75C24.2141 23.5011 24.6592 23.3167 24.9874 22.9885C25.3156 22.6604 25.5 22.2152 25.5 21.7511V8.75111C25.5002 8.6275 25.4698 8.50576 25.4116 8.39672C25.3534 8.28768 25.2691 8.19472 25.1663 8.12611ZM9.2725 15.7511L1.5 21.2386V10.2074L9.2725 15.7511ZM10.8063 16.5011H14.6938L22.48 22.0011H3.02L10.8063 16.5011ZM16.2275 15.7511L24 10.2074V21.2386L16.2275 15.7511ZM12.75 1.65236L23.4287 8.77736L14.6913 15.0011H10.8088L2.07125 8.77236L12.75 1.65236Z"
                                    fill="#DE3638" />
                            </svg></span>support@ana-sports.com</a></p>
                <p class="mt-3"><a class="text-white inline-flex items-center gap-2" href="tel:9311048371"><span
                            class="icon-telephone"><svg width="25" height="25" viewBox="0 0 25 25" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M15.025 1.55528C15.0504 1.46004 15.0943 1.37074 15.1542 1.29249C15.2142 1.21423 15.2889 1.14856 15.3743 1.09922C15.4596 1.04988 15.5538 1.01784 15.6515 1.00494C15.7493 0.992042 15.8486 0.998529 15.9438 1.02403C17.7552 1.49656 19.408 2.44348 20.7318 3.76726C22.0556 5.09103 23.0025 6.7438 23.475 8.55528C23.5005 8.65049 23.507 8.74979 23.4941 8.84751C23.4812 8.94523 23.4492 9.03945 23.3998 9.12477C23.3505 9.2101 23.2848 9.28487 23.2066 9.3448C23.1283 9.40473 23.039 9.44864 22.9438 9.47403C22.8806 9.49086 22.8154 9.49927 22.75 9.49903C22.5848 9.49913 22.4241 9.44466 22.293 9.34407C22.1619 9.24349 22.0677 9.10242 22.025 8.94278C21.6193 7.38677 20.8061 5.96708 19.669 4.83003C18.532 3.69298 17.1123 2.87971 15.5563 2.47403C15.461 2.44864 15.3717 2.40473 15.2935 2.3448C15.2152 2.28487 15.1495 2.2101 15.1002 2.12478C15.0509 2.03945 15.0188 1.94523 15.0059 1.84751C14.993 1.74979 14.9995 1.65049 15.025 1.55528ZM14.5563 6.47403C16.375 6.95903 17.54 8.12403 18.025 9.94278C18.0677 10.1024 18.1619 10.2435 18.293 10.3441C18.4241 10.4447 18.5848 10.4991 18.75 10.499C18.8154 10.4993 18.8806 10.4909 18.9438 10.474C19.039 10.4486 19.1283 10.4047 19.2066 10.3448C19.2848 10.2849 19.3505 10.2101 19.3998 10.1248C19.4492 10.0394 19.4812 9.94523 19.4941 9.84751C19.507 9.74979 19.5005 9.65049 19.475 9.55528C18.85 7.21653 17.2825 5.64903 14.9438 5.02403C14.8486 4.99859 14.7493 4.99215 14.6516 5.00508C14.5539 5.018 14.4597 5.05005 14.3744 5.09938C14.289 5.14871 14.2143 5.21436 14.1543 5.29259C14.0944 5.37081 14.0505 5.46007 14.025 5.55528C13.9996 5.65049 13.9931 5.74978 14.0061 5.84748C14.019 5.94517 14.051 6.03937 14.1004 6.12468C14.1497 6.21 14.2153 6.28476 14.2936 6.3447C14.3718 6.40464 14.4611 6.44859 14.5563 6.47403ZM24.4863 18.599C24.2721 20.2331 23.4702 21.7332 22.2305 22.819C20.9907 23.9049 19.398 24.5021 17.75 24.499C7.96251 24.499 1.24652e-05 16.5365 1.24652e-05 6.74903C-0.00314473 5.10159 0.593537 3.50933 1.6786 2.26968C2.76367 1.03003 4.2629 0.227784 5.89626 0.012783C6.27226 -0.03291 6.65293 0.0446194 6.98111 0.233727C7.30929 0.422835 7.56725 0.713312 7.71626 1.06153L10.3538 6.94903C10.4702 7.21557 10.5184 7.50693 10.494 7.79676C10.4695 8.0866 10.3732 8.36577 10.2138 8.60903C10.1977 8.6338 10.1802 8.65759 10.1613 8.68028L7.52751 11.8128C7.51152 11.8453 7.50321 11.881 7.50321 11.9172C7.50321 11.9534 7.51152 11.9891 7.52751 12.0215C8.48501 13.9815 10.54 16.0215 12.5275 16.9778C12.5607 16.9929 12.5969 17.0001 12.6334 16.9988C12.6698 16.9975 12.7055 16.9877 12.7375 16.9703L15.8238 14.3453C15.8458 14.3261 15.8692 14.3086 15.8938 14.2928C16.1359 14.1313 16.4146 14.0328 16.7044 14.0062C16.9943 13.9796 17.2862 14.0256 17.5538 14.1403L23.4588 16.7865C23.8024 16.9388 24.0878 17.1977 24.2728 17.5248C24.4579 17.8519 24.5327 18.2298 24.4863 18.6028V18.599ZM23 18.414C23.0042 18.3618 22.9919 18.3095 22.9647 18.2646C22.9376 18.2198 22.897 18.1846 22.8488 18.164L16.9425 15.5178C16.9102 15.5053 16.8757 15.5 16.8411 15.5022C16.8066 15.5043 16.773 15.5139 16.7425 15.5303L13.6575 18.1553C13.635 18.174 13.6113 18.1915 13.5875 18.2078C13.3359 18.3756 13.045 18.4753 12.7434 18.4972C12.4417 18.5191 12.1395 18.4625 11.8663 18.3328C9.57126 17.224 7.28376 14.9578 6.17501 12.684C6.04461 12.4124 5.98656 12.1116 6.00648 11.811C6.02641 11.5103 6.12364 11.2199 6.28876 10.9678C6.30486 10.9427 6.32283 10.9189 6.34251 10.8965L8.97376 7.76028C8.9888 7.72754 8.99659 7.69194 8.99659 7.65591C8.99659 7.61988 8.9888 7.58427 8.97376 7.55153L6.34251 1.66278C6.32514 1.61549 6.29388 1.57455 6.25284 1.54533C6.2118 1.51611 6.16289 1.49997 6.11251 1.49903H6.08376C4.81244 1.66815 3.64613 2.29421 2.80263 3.26032C1.95913 4.22642 1.4961 5.46652 1.50001 6.74903C1.50001 15.709 8.79001 22.999 17.75 22.999C19.0327 23.0029 20.273 22.5397 21.2391 21.6959C22.2052 20.8522 22.8312 19.6856 23 18.414Z"
                                    fill="#DE3638" />
                            </svg></span>9311048371</a></p>
            </div>
        </div>
        <div class="hidden flex-wrap items-start gap-5 lg:flex">
            @if ($customization?->options)
                @foreach ($customization->options as $footerLinkSection)
                    <ul class="grid gap-5 text-sm">
                        @php
                            usort($footerLinkSection, function ($a, $b) {
                                return $a['sort_order'] - $b['sort_order'];
                            });
                        @endphp

                        @foreach ($footerLinkSection as $link)
                            <li>
                                <a href="{{ $link['url'] }}">
                                    {{ $link['title'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            @endif
        </div>

        <!-- For Mobile view -->
        <x-shop::accordion :is-active="false"
            class="block !w-full !border-b !border-b-white lg:hidden">
            <x-slot:header
                class="font-medium">
                @lang('shop::app.components.layouts.footer.footer-content')
            </x-slot>

            <x-slot:content class="grid gap-2.5 grid-cols-2 !bg-transparent !pb-3 !p-0">
                @if ($customization?->options)
                    @foreach ($customization->options as $footerLinkSection)
                        <ul class="flex flex-col justify-start gap-5 text-sm">
                            @php
                                usort($footerLinkSection, function ($a, $b) {
                                    return $a['sort_order'] - $b['sort_order'];
                                });
                            @endphp

                            @foreach ($footerLinkSection as $link)
                                <li>
                                    <a href="{{ $link['url'] }}" class="text-base font-medium">
                                        {{ $link['title'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endforeach
                @endif
            </x-slot>
        </x-shop::accordion>

        {!! view_render_event('bagisto.shop.layout.footer.newsletter_subscription.before') !!}

        <!-- News Letter subscription -->
        @if (core()->getConfigData('customer.settings.newsletter.subscription'))
            <div class="grid gap-2.5 max-w-full lg:max-w-[360px] xl:max-w-[500px]">
                <p class="text-sm font-secondary" role="heading" aria-level="2">
                    @lang('shop::app.components.layouts.footer.newsletter-text')
                </p>

                <p>
                    @lang('shop::app.components.layouts.footer.subscribe-stay-touch')
                </p>

                <div>
                    <x-shop::form :action="route('shop.subscription.store')" class="mt-2.5 rounded max-sm:mt-0">
                        <div class="relative w-full flex items-center gap-2">
                            <x-shop::form.control-group.control
                                type="email"
                                class="w-full bg-white border border-white py-3 px-4 leading-6 rounded !m-0"
                                name="email"
                                rules="required|email"
                                label="Email"
                                :aria-label="trans('shop::app.components.layouts.footer.email')"
                                placeholder="email@example.com"
                            />

                            <button
                                type="submit"
                                class="inline-flex w-max items-center py-1 md:py-3 px-4 leading-6 !rounded bg-[#902129] h-[38px] md:h-[50px]"
                            >
                                <svg width="17" height="14" viewBox="0 0 17 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.7633 0.254972C10.6204 0.426158 10.5395 0.658181 10.5384 0.900532C10.5374 1.14288 10.6161 1.37594 10.7575 1.54897L14.32 5.83097H0.761666C0.559659 5.83097 0.365927 5.92727 0.223086 6.09868C0.0802464 6.27008 0 6.50256 0 6.74497C0 6.98738 0.0802464 7.21986 0.223086 7.39127C0.365927 7.56268 0.559659 7.65897 0.761666 7.65897H14.3142L10.7517 11.945C10.6114 12.1188 10.5335 12.3515 10.5346 12.5934C10.5357 12.8353 10.6157 13.067 10.7575 13.239C10.8283 13.323 10.9122 13.3895 11.0044 13.4347C11.0966 13.4798 11.1954 13.5027 11.2949 13.502C11.3945 13.5014 11.4931 13.4772 11.5849 13.4309C11.6767 13.3845 11.76 13.3169 11.83 13.232L16.6583 7.39597C16.7243 7.31139 16.7778 7.21401 16.8167 7.10797C16.8562 6.99676 16.8761 6.87689 16.875 6.75597C16.8751 6.51688 16.7974 6.2872 16.6583 6.11597L11.83 0.279972C11.7616 0.193659 11.6797 0.124483 11.5889 0.0764541C11.4981 0.0284248 11.4002 0.00249695 11.301 0.000171626C11.2018 -0.00215369 11.1032 0.0191702 11.0109 0.0629081C10.9186 0.106646 10.8344 0.171929 10.7633 0.254972Z" fill="#FFD0DE"/>
                                </svg>

                            </button>
                        </div>
                         <x-shop::form.control-group.error control-name="email" />
                        <h2 class="text-sm font-secondary mt-6 mb-3" role="heading" aria-level="2">
                            We Accept
                        </h2>
                        <div class="flex items-center gap-4 max-w-[300px] lg:max-w-full">
                            <img src="{{ asset('storage/theme/13/allCard.png') }}" alt="card"
                                class="object-contain w-full" width="150" height="75" />
                        </div>
                    </x-shop::form>
                </div>
            </div>
        @endif

        {!! view_render_event('bagisto.shop.layout.footer.newsletter_subscription.after') !!}
    </div>

    <div class="flex justify-between bg-black text-white text-xs md:text-sm px-[60px] py-3.5 max-md:justify-center max-sm:px-5">
        {!! view_render_event('bagisto.shop.layout.footer.footer_text.before') !!}

        <p class="">
            @lang('shop::app.components.layouts.footer.footer-text', ['current_year' => date('Y')])
        </p>

        {!! view_render_event('bagisto.shop.layout.footer.footer_text.after') !!}
    </div>
</footer>

{!! view_render_event('bagisto.shop.layout.footer.after') !!}
