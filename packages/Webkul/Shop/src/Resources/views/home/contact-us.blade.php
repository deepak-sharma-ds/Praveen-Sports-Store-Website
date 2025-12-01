<!-- Page Layout -->
<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.home.contact.title')
    </x-slot>

    <div class="container mt-8 max-1239:px-5 max-md:mt-6 max-md:px-4">

        <!-- 2 Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

            <div class="m-auto w-full max-w-[870px] rounded-xl border border-zinc-200 p-16 px-[90px] max-md:px-8 max-md:py-8 max-sm:border-none max-sm:p-0">

                <h1 class="font-secondary text-4xl max-md:text-3xl max-sm:text-xl">
                    @lang('shop::app.home.contact.title')
                </h1>

                <p class="mt-4 text-xl text-zinc-500 max-sm:mt-1 max-sm:text-sm">
                    @lang('shop::app.home.contact.about')
                </p>

                <!-- LEFT: CONTACT FORM -->
                <div class="mt-14 rounded max-sm:mt-8">
                    <x-shop::form :action="route('shop.home.contact_us.send_mail')">

                        <!-- Name -->
                        <x-shop::form.control-group>
                            <x-shop::form.control-group.label class="required">
                                @lang('shop::app.home.contact.name')
                            </x-shop::form.control-group.label>

                            <x-shop::form.control-group.control type="text" class="px-6 py-4" name="name"
                                rules="required" :placeholder="trans('shop::app.home.contact.name')" />
                            <x-shop::form.control-group.error control-name="name" />
                        </x-shop::form.control-group>

                        <!-- Email -->
                        <x-shop::form.control-group>
                            <x-shop::form.control-group.label class="required">
                                @lang('shop::app.home.contact.email')
                            </x-shop::form.control-group.label>

                            <x-shop::form.control-group.control type="email" class="px-6 py-4" name="email"
                                rules="required|email" :placeholder="trans('shop::app.home.contact.email')" />
                            <x-shop::form.control-group.error control-name="email" />
                        </x-shop::form.control-group>

                        <!-- Contact -->
                        <x-shop::form.control-group>
                            <x-shop::form.control-group.label>
                                @lang('shop::app.home.contact.phone-number')
                            </x-shop::form.control-group.label>

                            <x-shop::form.control-group.control type="text" class="px-6 py-4" name="contact"
                                rules="phone" :placeholder="trans('shop::app.home.contact.phone-number')" />
                            <x-shop::form.control-group.error control-name="contact" />
                        </x-shop::form.control-group>

                        <!-- Location -->
                        <x-shop::form.control-group>
                            <x-shop::form.control-group.label>
                                @lang('shop::app.home.contact.location')
                            </x-shop::form.control-group.label>

                            <x-shop::form.control-group.control type="text" class="px-6 py-4" name="location"
                                :placeholder="trans('shop::app.home.contact.location')" />
                            <x-shop::form.control-group.error control-name="location" />
                        </x-shop::form.control-group>

                        <!-- Message -->
                        <x-shop::form.control-group>
                            <x-shop::form.control-group.label class="required">
                                @lang('shop::app.home.contact.desc')
                            </x-shop::form.control-group.label>

                            <x-shop::form.control-group.control type="textarea"
                                class="px-6 py-5 max-md:py-3 max-sm:py-3.5" name="message" rules="required|max:200"
                                :label="trans('shop::app.home.contact.message')" :placeholder="trans('shop::app.home.contact.describe-here')" :aria-label="trans('shop::app.home.contact.message')" aria-required="true"
                                rows="10" />
                            <x-shop::form.control-group.error control-name="message" />
                        </x-shop::form.control-group>

                        <!-- Captcha -->
                        @if (core()->getConfigData('customer.captcha.credentials.status'))
                            <x-shop::form.control-group class="mt-5">
                                {!! \Webkul\Customer\Facades\Captcha::render() !!}
                                <x-shop::form.control-group.error control-name="g-recaptcha-response" />
                            </x-shop::form.control-group>
                        @endif

                        <!-- Submit -->
                        <div class="mt-6">
                            <button class="w-full max-w-[350px] py-4 rounded-xl mx-auto block text-base border border-[#902129] text-[#902129] py-3 px-10 text-center"
                                type="submit">
                                @lang('shop::app.home.contact.submit')
                            </button>
                        </div>

                    </x-shop::form>
                </div>

                <!-- RIGHT: CONTACT DETAILS + MAP -->
                <div class="mt-14 rounded max-sm:mt-8">
                    <!-- ADDRESS -->
                    <div class="p-8 rounded-xl">
                        <p class="flex items-start gap-3 text-lg">
                            <svg width="26" height="26" fill="none" stroke="#000" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path d="M12 2C8.1 2 5 5.1 5 9c0 4.9 6 12 6 12s6-7.1 6-12c0-3.9-3.1-7-7-7z" />
                                <circle cx="12" cy="9" r="2.5" />
                            </svg>
                            AARYA ADRIJA SPORTS PVT. LTD.
                            Khasra No-115, Jalaluddin
                            Maksoodpur, Gaavandi, Meerut, Uttar Pradesh-250002
                        </p>

                        <p class="flex items-center gap-3 mt-4">
                            <svg width="26" height="24" fill="#000">
                                <path d="M12 12L24 4v16H0V4l12 8z" />
                            </svg>
                            <a href="mailto:support@ana-sports.com" class="text-lg">
                                support@ana-sports.com
                            </a>
                        </p>

                        <p class="flex items-center gap-3 mt-4">
                            <svg width="25" height="25" fill="#000">
                                <path
                                    d="M6.6 0C9 0 11 2 11.4 4.4C11.5 5.1 11.3 5.8 10.9 6.4L9 9C10.7 12 13 14.2 16 16l2.6-1.9c.6-.4 1.3-.6 2-.5C23 14 25 16 25 18.4c0 1-.4 1.9-1.1 2.6c-.7.7-1.7 1.1-2.6 1.1C9.8 22.1 0 12.3 0 1.1C0 .8.1.4.4.1S1 0 1.3 0H6.6z" />
                            </svg>
                            <a href="tel:9311048371" class="text-lg">9311048371</a>
                        </p>
                        <!-- GOOGLE MAP -->
                        {{-- <div class="overflow-hidden rounded-xl border border-zinc-200 shadow-sm"> --}}
                        <iframe src="https://www.google.com/maps?q=Arya sports, Jamuna Nagar, Hapur Rd, Hapur Chungi, Meerut, Uttar Pradesh 250002, India&output=embed" width="100%" height="350"
                            style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                        {{-- </div> --}}
                    </div>
                </div>
            </div>


        </div>
    </div>

    @push('scripts')
        {!! \Webkul\Customer\Facades\Captcha::renderJS() !!}
    @endpush
</x-shop::layouts>
