<!-- Page Layout -->
<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.home.contact.title')
    </x-slot>

    <div class="container mt-8 max-1239:px-5 max-md:mt-6 max-md:px-4">
        <!-- Form Container -->
        <div
            class="m-auto w-full max-w-[870px] rounded-xl border border-zinc-200 p-16 px-[90px] max-md:px-8 max-md:py-8 max-sm:border-none max-sm:p-0">
            <h1 class="font-secondary text-4xl max-md:text-3xl max-sm:text-xl">
                @lang('shop::app.home.contact.title')
            </h1>

            <p class="mt-4 text-xl text-zinc-500 max-sm:mt-1 max-sm:text-sm">
                @lang('shop::app.home.contact.about')
            </p>

            <div class="mt-14 rounded max-sm:mt-8">
                <!-- Contact Form -->
                <x-shop::form :action="route('shop.home.contact_us.send_mail')">
                    <!-- Name -->
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label class="required">
                            @lang('shop::app.home.contact.name')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control type="text" class="px-6 py-5 max-md:py-3 max-sm:py-3.5"
                            name="name" rules="required" :value="old('name')" :label="trans('shop::app.home.contact.name')" :placeholder="trans('shop::app.home.contact.name')"
                            :aria-label="trans('shop::app.home.contact.name')" aria-required="true" />

                        <x-shop::form.control-group.error control-name="name" />
                    </x-shop::form.control-group>

                    <!-- Email -->
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label class="required">
                            @lang('shop::app.home.contact.email')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control type="email" class="px-6 py-5 max-md:py-3 max-sm:py-3.5"
                            name="email" rules="required|email" :value="old('email')" :label="trans('shop::app.home.contact.email')" :placeholder="trans('shop::app.home.contact.email')"
                            :aria-label="trans('shop::app.home.contact.email')" aria-required="true" />

                        <x-shop::form.control-group.error control-name="email" />
                    </x-shop::form.control-group>

                    <!-- Contact -->
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label>
                            @lang('shop::app.home.contact.phone-number')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control type="text" class="px-6 py-5 max-md:py-3 max-sm:py-3.5"
                            name="contact" rules="phone" :value="old('contact')" :label="trans('shop::app.home.contact.phone-number')" :placeholder="trans('shop::app.home.contact.phone-number')"
                            :aria-label="trans('shop::app.home.contact.phone-number')" />

                        <x-shop::form.control-group.error control-name="contact" />
                    </x-shop::form.control-group>

                    <!-- Location -->
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label>
                            @lang('shop::app.home.contact.location')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control type="text" class="px-6 py-5 max-md:py-3 max-sm:py-3.5"
                            name="location" rules="phone" :value="old('location')" :label="trans('shop::app.home.contact.location')" :placeholder="trans('shop::app.home.contact.location')"
                            :aria-label="trans('shop::app.home.contact.location')" />

                        <x-shop::form.control-group.error control-name="location" />
                    </x-shop::form.control-group>

                    <!-- Message -->
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label class="required">
                            @lang('shop::app.home.contact.desc')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control type="textarea" class="px-6 py-5 max-md:py-3 max-sm:py-3.5"
                            name="message" rules="required|max:200" :label="trans('shop::app.home.contact.message')" :placeholder="trans('shop::app.home.contact.describe-here')"
                            :aria-label="trans('shop::app.home.contact.message')" aria-required="true" rows="10" />

                        <x-shop::form.control-group.error control-name="message" />
                    </x-shop::form.control-group>

                    <!-- Captcha -->
                    @if (core()->getConfigData('customer.captcha.credentials.status'))
                        <x-shop::form.control-group class="mt-5">
                            {!! \Webkul\Customer\Facades\Captcha::render() !!}

                            <x-shop::form.control-group.error control-name="g-recaptcha-response" />
                        </x-shop::form.control-group>
                    @endif

                    <!-- Submit Button -->
                    <div class="mt-8 flex flex-wrap items-center gap-9 max-sm:justify-center max-sm:text-center">
                        <button
                            class="primary-button m-0 mx-auto block w-full max-w-[374px] rounded-2xl px-11 py-4 text-center text-base max-md:max-w-full max-md:rounded-lg max-md:py-3 max-sm:py-1.5 ltr:ml-0 rtl:mr-0"
                            type="submit">
                            @lang('shop::app.home.contact.submit')
                        </button>
                    </div>
                </x-shop::form>
            </div>

            <div class="mt-14 rounded max-sm:mt-8">
                <!-- Contact Address/Details -->
                <div class="container">
                    <p class="mt-3 text-black inline-flex items-center gap-2">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M12 2C8.13401 2 5 5.13401 5 9C5 13.9231 11.0375 21.2195 11.2955 21.5212C11.6855 21.9812 12.3145 21.9812 12.7045 21.5212C12.9625 21.2195 19 13.9231 19 9C19 5.13401 15.866 2 12 2Z"
                                stroke="#000000" stroke-width="2" />
                            <circle cx="12" cy="9" r="2.5" stroke="#000000" stroke-width="2" />
                        </svg>Jalaluddinpur Masoodpur Gawri, Post - Kinanagar, Thana - Bhawanpur, Pin code -
                        250004
                    </p>
                    <p class="mt-3"><a class="text-black inline-flex items-center gap-2"
                            href="mailto:support@anasports.com"><span class="icon-telephone"><svg width="26"
                                    height="24" viewBox="0 0 26 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M25.1663 8.12611L13.1663 0.126112C13.043 0.043883 12.8982 0 12.75 0C12.6018 0 12.457 0.043883 12.3338 0.126112L0.333751 8.12611C0.230923 8.19472 0.146646 8.28768 0.0884186 8.39672C0.0301909 8.50576 -0.000182402 8.6275 8.24085e-07 8.75111V21.7511C8.24085e-07 22.2152 0.184375 22.6604 0.512564 22.9885C0.840753 23.3167 1.28587 23.5011 1.75 23.5011H23.75C24.2141 23.5011 24.6592 23.3167 24.9874 22.9885C25.3156 22.6604 25.5 22.2152 25.5 21.7511V8.75111C25.5002 8.6275 25.4698 8.50576 25.4116 8.39672C25.3534 8.28768 25.2691 8.19472 25.1663 8.12611ZM9.2725 15.7511L1.5 21.2386V10.2074L9.2725 15.7511ZM10.8063 16.5011H14.6938L22.48 22.0011H3.02L10.8063 16.5011ZM16.2275 15.7511L24 10.2074V21.2386L16.2275 15.7511ZM12.75 1.65236L23.4287 8.77736L14.6913 15.0011H10.8088L2.07125 8.77236L12.75 1.65236Z"
                                        fill="#000000" />
                                </svg></span>support@anasports.com</a></p>
                    <p class="mt-3"><a class="text-black inline-flex items-center gap-2" href="tel:9311048371"><span
                                class="icon-telephone"><svg width="25" height="25" viewBox="0 0 25 25"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M15.025 1.55528C15.0504 1.46004 15.0943 1.37074 15.1542 1.29249C15.2142 1.21423 15.2889 1.14856 15.3743 1.09922C15.4596 1.04988 15.5538 1.01784 15.6515 1.00494C15.7493 0.992042 15.8486 0.998529 15.9438 1.02403C17.7552 1.49656 19.408 2.44348 20.7318 3.76726C22.0556 5.09103 23.0025 6.7438 23.475 8.55528C23.5005 8.65049 23.507 8.74979 23.4941 8.84751C23.4812 8.94523 23.4492 9.03945 23.3998 9.12477C23.3505 9.2101 23.2848 9.28487 23.2066 9.3448C23.1283 9.40473 23.039 9.44864 22.9438 9.47403C22.8806 9.49086 22.8154 9.49927 22.75 9.49903C22.5848 9.49913 22.4241 9.44466 22.293 9.34407C22.1619 9.24349 22.0677 9.10242 22.025 8.94278C21.6193 7.38677 20.8061 5.96708 19.669 4.83003C18.532 3.69298 17.1123 2.87971 15.5563 2.47403C15.461 2.44864 15.3717 2.40473 15.2935 2.3448C15.2152 2.28487 15.1495 2.2101 15.1002 2.12478C15.0509 2.03945 15.0188 1.94523 15.0059 1.84751C14.993 1.74979 14.9995 1.65049 15.025 1.55528ZM14.5563 6.47403C16.375 6.95903 17.54 8.12403 18.025 9.94278C18.0677 10.1024 18.1619 10.2435 18.293 10.3441C18.4241 10.4447 18.5848 10.4991 18.75 10.499C18.8154 10.4993 18.8806 10.4909 18.9438 10.474C19.039 10.4486 19.1283 10.4047 19.2066 10.3448C19.2848 10.2849 19.3505 10.2101 19.3998 10.1248C19.4492 10.0394 19.4812 9.94523 19.4941 9.84751C19.507 9.74979 19.5005 9.65049 19.475 9.55528C18.85 7.21653 17.2825 5.64903 14.9438 5.02403C14.8486 4.99859 14.7493 4.99215 14.6516 5.00508C14.5539 5.018 14.4597 5.05005 14.3744 5.09938C14.289 5.14871 14.2143 5.21436 14.1543 5.29259C14.0944 5.37081 14.0505 5.46007 14.025 5.55528C13.9996 5.65049 13.9931 5.74978 14.0061 5.84748C14.019 5.94517 14.051 6.03937 14.1004 6.12468C14.1497 6.21 14.2153 6.28476 14.2936 6.3447C14.3718 6.40464 14.4611 6.44859 14.5563 6.47403ZM24.4863 18.599C24.2721 20.2331 23.4702 21.7332 22.2305 22.819C20.9907 23.9049 19.398 24.5021 17.75 24.499C7.96251 24.499 1.24652e-05 16.5365 1.24652e-05 6.74903C-0.00314473 5.10159 0.593537 3.50933 1.6786 2.26968C2.76367 1.03003 4.2629 0.227784 5.89626 0.012783C6.27226 -0.03291 6.65293 0.0446194 6.98111 0.233727C7.30929 0.422835 7.56725 0.713312 7.71626 1.06153L10.3538 6.94903C10.4702 7.21557 10.5184 7.50693 10.494 7.79676C10.4695 8.0866 10.3732 8.36577 10.2138 8.60903C10.1977 8.6338 10.1802 8.65759 10.1613 8.68028L7.52751 11.8128C7.51152 11.8453 7.50321 11.881 7.50321 11.9172C7.50321 11.9534 7.51152 11.9891 7.52751 12.0215C8.48501 13.9815 10.54 16.0215 12.5275 16.9778C12.5607 16.9929 12.5969 17.0001 12.6334 16.9988C12.6698 16.9975 12.7055 16.9877 12.7375 16.9703L15.8238 14.3453C15.8458 14.3261 15.8692 14.3086 15.8938 14.2928C16.1359 14.1313 16.4146 14.0328 16.7044 14.0062C16.9943 13.9796 17.2862 14.0256 17.5538 14.1403L23.4588 16.7865C23.8024 16.9388 24.0878 17.1977 24.2728 17.5248C24.4579 17.8519 24.5327 18.2298 24.4863 18.6028V18.599ZM23 18.414C23.0042 18.3618 22.9919 18.3095 22.9647 18.2646C22.9376 18.2198 22.897 18.1846 22.8488 18.164L16.9425 15.5178C16.9102 15.5053 16.8757 15.5 16.8411 15.5022C16.8066 15.5043 16.773 15.5139 16.7425 15.5303L13.6575 18.1553C13.635 18.174 13.6113 18.1915 13.5875 18.2078C13.3359 18.3756 13.045 18.4753 12.7434 18.4972C12.4417 18.5191 12.1395 18.4625 11.8663 18.3328C9.57126 17.224 7.28376 14.9578 6.17501 12.684C6.04461 12.4124 5.98656 12.1116 6.00648 11.811C6.02641 11.5103 6.12364 11.2199 6.28876 10.9678C6.30486 10.9427 6.32283 10.9189 6.34251 10.8965L8.97376 7.76028C8.9888 7.72754 8.99659 7.69194 8.99659 7.65591C8.99659 7.61988 8.9888 7.58427 8.97376 7.55153L6.34251 1.66278C6.32514 1.61549 6.29388 1.57455 6.25284 1.54533C6.2118 1.51611 6.16289 1.49997 6.11251 1.49903H6.08376C4.81244 1.66815 3.64613 2.29421 2.80263 3.26032C1.95913 4.22642 1.4961 5.46652 1.50001 6.74903C1.50001 15.709 8.79001 22.999 17.75 22.999C19.0327 23.0029 20.273 22.5397 21.2391 21.6959C22.2052 20.8522 22.8312 19.6856 23 18.414Z"
                                        fill="#000000" />
                                </svg></span>9311048371</a></p>
                    <!-- GOOGLE MAP -->
                    <div class="overflow-hidden rounded-xl border border-zinc-200 shadow-sm">
                        <iframe src="https://www.google.com/maps?q=250004&output=embed" width="100%" height="350"
                            style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        {!! \Webkul\Customer\Facades\Captcha::renderJS() !!}
    @endpush
</x-shop::layouts>
