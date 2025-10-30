<v-reviews-carousel src="{{ $src }}" title="{{ $title }}" navigation-link="{{ $navigationLink ?? '' }}">
</v-reviews-carousel>

@pushOnce('scripts')
    <script type="text/x-template" id="v-reviews-carousel-template">
        <div v-if="!isLoading && reviews.length" class="container mt-14 max-lg:px-8">
            <div class="relative">
                <div
                    ref="swiperContainer"
                    class="scrollbar-hide flex gap-10 overflow-auto scroll-smooth"
                >
                    <div
                        class="min-w-[300px] max-w-[300px] p-4 border rounded-lg bg-white shadow-sm text-center"
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
                    class="icon-arrow-left-stylish absolute -left-10 top-9 flex h-[50px] w-[50px] cursor-pointer items-center justify-center rounded-full border border-black bg-white text-2xl transition hover:bg-black hover:text-white"
                    role="button"
                    aria-label="Previous"
                    @click="swipeLeft"
                ></span>

                <span
                    class="icon-arrow-right-stylish absolute -right-6 top-9 flex h-[50px] w-[50px] cursor-pointer items-center justify-center rounded-full border border-black bg-white text-2xl transition hover:bg-black hover:text-white"
                    role="button"
                    aria-label="Next"
                    @click="swipeRight"
                ></span>
            </div>
        </div>

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
                    this.$refs.swiperContainer.scrollLeft -= this.offset;
                },

                swipeRight() {
                    this.$refs.swiperContainer.scrollLeft += this.offset;
                },
            },
        });
    </script>
@endPushOnce
