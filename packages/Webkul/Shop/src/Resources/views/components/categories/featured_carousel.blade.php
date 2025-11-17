<v-categories-featured_carousel src="{{ $src }}" title="{{ $title }}"
    navigation-link="{{ $navigationLink ?? '' }}">
    <x-shop::shimmer.categories.featured_carousel :count="4" :navigation-link="$navigationLink ?? false" />
</v-categories-featured_carousel>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-categories-featured_carousel-template"
    >
    <section class="bg-[#EDEDED]">
        <div class="text-center mb-10 font-secondary text-[32px] uppercase">
            <h2>Featured Categories</h2>
        </div>
        <div
            class="container max-lg:px-8 max-md:!px-0"
            v-if="! isLoading && categories?.length"
        >
            <div class="relative">
                <div
                    ref="swiperContainer"
                    class="scrollbar-hide flex gap-4 overflow-auto scroll-smooth"
                >
                    <div
                        class="relative w-full"
                        v-for="category in categories"
                    >
                        <a
                            :href="category.slug"
                            class="w-full overflow-hidden rounded-md"
                            :aria-label="category.name"
                        >
                            <!-- <x-shop::media.images.lazy
                                ::src="category.logo?.original_image_url || fallback"
                                ::srcset="`
                                    ${(category.logo?.small_image_url || fallback)} 200w,
                                    ${(category.logo?.medium_image_url || fallback)} 400w,
                                    ${(category.logo?.large_image_url || fallback)} 800w,
                                    ${(category.logo?.original_image_url || fallback)} 1200w
                                `"
                                sizes="(max-width: 640px) 200px, (max-width: 1024px) 400px, 800px"
                                width="400"
                                height="400"
                                class="aspect-[9/16] w-full h-auto object-cover rounded-md"
                                ::alt="category.name"
                            /> -->
                            <x-shop::media.images.lazy
                                ::src="category.logo?.original_image_url || fallback"
                                class="w-auto h-auto rounded-md"
                                ::alt="category.name"
                            />

                        </a>

                        <a
                            :href="category.slug"
                            class="rounded-b-md absolute bottom-0 left-0 w-full text-lg p-4 leading-snug text-left text-white font-secondary bg-gradient-to-t from-black to-transparent"
                        >
                            <p
                                v-text="category.name"
                            >
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
                :count="4"
                :navigation-link="$navigationLink ?? false"
            />
        </template>
    </script>

    <script type="module">
        app.component('v-categories-featured_carousel', {
            template: '#v-categories-featured_carousel-template',

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

                            this.categories = response.data.data;
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
