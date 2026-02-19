<v-categories-carousel src="{{ $src }}" title="{{ $title }}" navigation-link="{{ $navigationLink ?? '' }}">
    <x-shop::shimmer.categories.carousel :count="3" :navigation-link="$navigationLink ?? false" />
</v-categories-carousel>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-categories-carousel-template"
    >
    <section class="bg-[#EDEDED] py-10 md:py-14">
        <div class="text-center mb-5 lg:mb-10 font-secondary text-[32px] uppercase px-5">
            <h2>Made for Match Winners</h2>
        </div>
        <div
            class="px-4 lg:px-[60px]"
            v-if="! isLoading && categories?.length"
        >
            <div class="relative">
                <div
                    ref="swiperContainer"
                    class="scrollbar-hide flex max-md:flex-wrap justify-center gap-10 overflow-auto scroll-smooth max-lg:gap-4"
                >
                    <div
                        class="w-full md:w-1/3 grid grid-cols-1 justify-items-center text-center gap-4 font-medium max-md:gap-2.5 max-md:first:ml-4 max-sm:gap-1.5"
                        v-for="category in categories"
                    >
                        <a
                            :href="category.slug"
                            class="relative w-full inline-block pt-[100%] before:content-[''] before:absolute before:top-0 before:left-[10%] before:right-[10%] before:bottom-0 before:bg-gradient-to-t before:from-[#E6BF42] before:to-[#90212900] before:rounded-t-full"
                            :aria-label="category.name"
                        >
                            <!-- <x-shop::media.images.lazy
                                ::src="category.logo?.small_image_url || fallback"
                                ::srcset="`
                                    ${(category.logo?.small_image_url || fallback)} 60w,
                                    ${(category.logo?.medium_image_url || fallback)} 110w,
                                    ${(category.logo?.large_image_url || fallback)} 300w
                                `"
                                sizes="(max-width: 640px) 60px, 110px"
                                width="110"
                                height="110"
                                class="w-full h-auto absolute bottom-0 left-0 object-contain object-bottom"
                                ::alt="category.name"
                            /> -->
                            <x-shop::media.images.lazy
                                ::src="category.logo?.original_image_url || fallback"
                                class="w-full h-auto absolute bottom-0 left-0 object-contain object-bottom"
                                ::alt="category.name"
                            />
                        </a>

                        <a
                            :href="category.slug"
                            class=""
                        >
                            <p
                                class="text-base text-[#0F1D71] uppercase font-secondary"
                                v-text="category.name"
                            >
                            </p>
                            <p class="uppercase inline-flex items-center gap-2.5 text-[15px]">view all products
                                <svg width="8" height="16" viewBox="0 0 8 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.93357 7.50609L4.95302 7.56225L4.95719 7.5605L4.93774 7.61665C4.92801 7.64649 3.94391 10.5369 1.37662 13.6694C0.949575 14.1883 0.490781 14.6733 0.00558099 15.1207C1.53715 14.4552 2.94075 13.5317 4.10738 12.407C6.67467 9.92157 7.65876 7.62833 7.66849 7.60466L7.68795 7.5601L7.68378 7.56149L7.66432 7.51694C7.64112 7.46341 6.65257 5.18264 4.1032 2.71464C2.9363 1.58874 1.53231 0.66524 0 4.00543e-05C0.485731 0.44794 0.944995 0.933538 1.37244 1.45334C3.9218 4.56404 4.91001 7.43761 4.93322 7.50509L4.93357 7.50609Z" fill="currentColor"/>
                                </svg>
                            </p>
                        </a>
                    </div>
                </div>

                <!-- <span
                    class="icon-arrow-left-stylish absolute -left-10 top-9 flex h-[50px] w-[50px] cursor-pointer items-center justify-center rounded-full border border-black bg-white text-2xl transition hover:bg-black hover:text-white max-lg:-left-7 max-md:hidden"
                    role="button"
                    aria-label="@lang('shop::components.carousel.previous')"
                    tabindex="0"
                    @click="swipeLeft"
                >
                </span>

                <span
                    class="icon-arrow-right-stylish absolute -right-6 top-9 flex h-[50px] w-[50px] cursor-pointer items-center justify-center rounded-full border border-black bg-white text-2xl transition hover:bg-black hover:text-white max-lg:-right-7 max-md:hidden"
                    role="button"
                    aria-label="@lang('shop::components.carousel.next')"
                    tabindex="0"
                    @click="swipeRight"
                >
                </span> -->
            </div>
        </div>
    </section>

        <!-- Category Carousel Shimmer -->
        <template v-if="isLoading">
            <x-shop::shimmer.categories.carousel
                :count="3"
                :navigation-link="$navigationLink ?? false"
            />
        </template>
    </script>

    <script type="module">
        app.component('v-categories-carousel', {
            template: '#v-categories-carousel-template',

            props: [
                'src',
                'title',
                'navigationLink',
            ],

            data() {
                return {
                    isLoading: true,

                    categories: [],

                    offset: 323,

                    fallback: "{{ bagisto_asset('images/small-product-placeholder.webp') }}"
                };
            },

            mounted() {
                this.getCategories();
            },

            methods: {
                getCategories() {
                    this.$axios.get(this.src)
                        .then(response => {
                            this.isLoading = false;

                            // this.categories = response.data.data;
                            // Filtered unwanted categories
                            this.categories = response.data.data.filter(category => {
                                return !['15-off-deals', '20-off-deals'].includes(category.slug);
                            });
                        }).catch(error => {
                            console.log(error);
                        });
                },

                swipeLeft() {
                    const container = this.$refs.swiperContainer;

                    container.scrollLeft -= this.offset;
                },

                swipeRight() {
                    const container = this.$refs.swiperContainer;

                    container.scrollLeft += this.offset;
                },
            },
        });
    </script>
@endPushOnce
