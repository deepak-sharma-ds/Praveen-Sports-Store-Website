<v-reviews-carousel src="{{ $src }}" title="{{ $title }}" navigation-link="{{ $navigationLink ?? '' }}">
</v-reviews-carousel>

@pushOnce('scripts')
    {{-- <script type="text/x-template" id="v-reviews-carousel-template">
        <section class="bg-white py-10 md:py-14">
            <div v-if="!isLoading && reviews.length" class="px-4 lg:px-[60px]">
                <div class="relative">
                    <div
                        ref="swiperContainer"
                        class="scrollbar-hide flex overflow-auto scroll-smooth mb-5"
                    >
                        <div
                            class="review-item flex-shrink-0 w-full md:w-1/2 md:pr-4" style="padding-right: 16px;"
                            v-for="review in reviews"
                            :key="review.id"
                        >
                            <h5 class="font-semibold text-lg mb-2" v-text="review.title"></h5>
                            <p class="text-gray-600 text-sm mb-3" v-text="review.comment"></p>
                            <p class="text-yellow-500 mb-2">
                                <span v-for="n in 5" :key="n">
                                    <span v-if="n <= review.rating">★</span>
                                    <span v-else>☆</span>
                                </span>
                            </p>
                            <p class="text-gray-500 text-sm">
                                — <span v-text="review.name"></span>
                            </p>
                        </div>
                    </div>

                    <!-- Navigation Arrows -->
                    <span
                        class="icon-arrow-left-stylish inline-flex h-7 w-7 cursor-pointer items-center justify-center bg-white text-2xl transition hover:bg-black hover:text-white mr-3"
                        role="button"
                        aria-label="Previous"
                        @click="swipeLeft"
                    ></span>

                    <span
                        class="icon-arrow-right-stylish inline-flex h-7 w-7 cursor-pointer items-center justify-center bg-white text-2xl transition hover:bg-black hover:text-white"
                        role="button"
                        aria-label="Next"
                        @click="swipeRight"
                    ></span>
                </div>
            </div>
        </section>

        <!-- Shimmer -->
        <template v-if="isLoading">

        </template>
    </script> --}}
    <script type="text/x-template" id="v-reviews-carousel-template">
    <section class="bg-white py-10 md:py-14">
        <div v-if="!isLoading && reviews.length" class="px-4 lg:px-[60px]">
            <div class="flex flex-wrap items-center gap-10">

                <!-- LEFT SIDE: Reviews Carousel -->
                <div class="relative flex-1 min-w-[60%]">
                    <div
                        ref="swiperContainer"
                        class="scrollbar-hide flex overflow-auto scroll-smooth mb-5"
                    >
                        <div
                            class="review-item flex-shrink-0 w-full md:w-1/2 md:pr-4" style="padding-right: 16px;"
                            v-for="review in reviews"
                            :key="review.id"
                        >
                            <h5 class="font-semibold text-lg mb-2" v-text="review.title"></h5>
                            <p class="text-yellow-500 mb-2">
                                <span v-for="n in 5" :key="n">
                                    <span v-if="n <= review.rating">★</span>
                                    <span v-else>☆</span>
                                </span>
                            </p>
                            <p class="text-gray-600 text-sm mb-3" v-text="review.comment"></p>
                            <p class="text-gray-500 text-sm">
                                — <span v-text="review.name"></span>
                            </p>
                        </div>
                    </div>

                    <!-- Navigation Arrows -->
                    <span
                        class="icon-arrow-left-stylish inline-flex h-7 w-7 cursor-pointer items-center justify-center bg-white text-2xl transition hover:bg-black hover:text-white mr-3"
                        role="button"
                        aria-label="Previous"
                        @click="swipeLeft"
                    ></span>

                    <span
                        class="icon-arrow-right-stylish inline-flex h-7 w-7 cursor-pointer items-center justify-center bg-white text-2xl transition hover:bg-black hover:text-white"
                        role="button"
                        aria-label="Next"
                        @click="swipeRight"
                    ></span>
                </div>

                <!-- RIGHT SIDE: Static Image -->
                <div class="flex-1 min-w-[35%] flex flex-col">
                    <img
                        src="{{ bagisto_asset('images/review-banner.png', 'shop') }}"
                        alt="Customer Reviews"
                        class="rounded-md max-w-full w-full h-auto object-cover"
                    />
                    <!-- <p class="mt-2.5">Stratos 1.1 5 Star Lite Cricket Bat</p> -->
                </div>
            </div>
        </div>
    </section>

    <!-- Shimmer -->
    <template v-if="isLoading">

    </template>
</script>


    <script type="module">
        app.component('v-reviews-carousel', {
            template: '#v-reviews-carousel-template',

            props: ['src', 'title', 'navigationLink'],

            data() {
                return {
                    isLoading: true,
                    reviews: [],
                    offset: 323,
                };
            },

            mounted() {
                this.getReviews();
            },

            methods: {
                getReviews() {
                    this.$axios.get(this.src)
                        .then(response => {
                            this.reviews = response.data.data ?? response.data;
                            this.isLoading = false;
                        })
                        .catch(error => {
                            console.error('Error fetching reviews:', error);
                        });
                },

                swipeLeft() {
                    this.$refs.swiperContainer.scrollLeft -= this.$refs.swiperContainer.clientWidth;
                },

                swipeRight() {
                    this.$refs.swiperContainer.scrollLeft += this.$refs.swiperContainer.clientWidth;
                },
            },
        });
    </script>
@endPushOnce
