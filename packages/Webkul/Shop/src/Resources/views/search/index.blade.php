<?php
$searchTitle = $suggestion ?? $query;
$title = $searchTitle ? trans('shop::app.search.title', ['query' => $searchTitle]) : trans('shop::app.search.results');
$searchInstead = $suggestion ? $query : null;
$isAllProductsPage = request()->routeIs('shop.all.product.index');
?>
<!-- SEO Meta Content -->
@push('meta')
    <meta name="description" content="{{ $title }}" />

    <meta name="keywords" content="{{ $title }}" />
@endPush

@if ($isAllProductsPage)
    @pushOnce('styles')
        <style>
            .seo-read-more-block {
                background: #ffffff;
                width: 100%;
                margin: 0 auto;
                font-size: 16px;
                line-height: 1.7;
                color: #333;
            }

            .seo-main-heading {
                font-size: 1.5rem;
                font-weight: 700;
                color: #111827;
                margin-bottom: 0.75rem;
                line-height: 1.3;
            }

            .seo-sub-heading {
                font-size: 1.1rem;
                font-weight: 600;
                color: #1f2937;
                margin-bottom: 0.5rem;
                margin-top: 0;
            }

            .seo-category-heading {
                font-size: 1rem;
                font-weight: 600;
                color: #1f2937;
                margin-bottom: 0.4rem;
                margin-top: 1rem;
            }

            .seo-text {
                color: #4b5563;
                font-size: 0.9rem;
                line-height: 1.7;
                margin-bottom: 0.75rem;
            }

            .seo-link {
                color: #2563eb;
                text-decoration: underline;
            }

            .seo-toggle-checkbox {
                display: none;
            }

            .seo-content-preview {
                display: block;
            }

            .seo-content-full {
                display: none;
            }

            .seo-read-more-block .seo-btn-read-more {
                display: inline-block !important;
            }

            .seo-read-more-block .seo-btn-read-less {
                display: none !important;
            }

            .seo-toggle-checkbox:checked~.seo-content-preview {
                display: none;
            }

            .seo-toggle-checkbox:checked~.seo-content-full {
                display: block;
            }

            .seo-toggle-checkbox:checked~.seo-btn-read-more {
                display: none !important;
            }

            .seo-toggle-checkbox:checked~.seo-btn-read-less {
                display: inline-block !important;
            }

            .seo-btn {
                display: inline-block;
                margin-top: 1rem;
                padding: 0.5rem 1.25rem;
                background-color: #111827;
                color: #ffffff;
                font-size: 0.875rem;
                font-weight: 500;
                border-radius: 3px;
                cursor: pointer;
                transition: background-color 0.2s ease;
                user-select: none;
            }

            .seo-btn:hover {
                background-color: #374151;
            }

            .seo-read-more-block .seo-list {
                padding-left: 1.5rem;
                margin-bottom: 0.75rem;
                list-style: disc;
            }
        </style>
    @endPushOnce
@endif

<x-shop::layouts :has-feature="false">
    <!-- Page Title -->
    <x-slot:title>
        {{ $title }}
    </x-slot>

    <div
        class="bg-[#EDEDED] container px-[60px] max-lg:px-8 max-sm:px-4 py-[80px] md:py-[150px] text-white text-center relative z-0 before:absolute before:inset-0 before:z-[1] before:bg-black/50 before:content-['']">
        <img src="{{ bagisto_asset('images/search-page-banner.jpg') }}" alt="Search Icon"
            class="absolute inset-0 h-full w-full object-cover object-center" />
        @if (request()->has('image-search'))
            @include('shop::search.images.results')
        @endif
        <div class="flex items-center text-center justify-center relative z-[2]">
            <h1 class="text-[24px] xl:text-5xl font-normal font-secondary">
                <span v-text="'{{ preg_replace('/[,\\"\\\']+/', '', $title) }}'"></span>
            </h1>
        </div>
        @if ($searchInstead)
            <form action="{{ route('shop.search.index', ['suggest' => false]) }}"
                class="flex max-w-[445px] items-center" role="search">
                <input type="text" name="query" class="hidden" value="{{ $searchInstead }}">

                <input type="text" name="suggest" class="hidden" value="0">

                <p class="mt-1 text-sm text-gray-600">
                    {{ trans('shop::app.search.suggest') }}

                    <button type="submit" class="text-blue-600 hover:text-blue-800 hover:underline"
                        aria-label="{{ trans('shop::app.components.layouts.header.desktop.bottom.submit') }}">
                        {{ $searchInstead }}
                    </button>
                </p>
            </form>
        @endif
    </div>

    <!-- Product Listing -->
    <v-search>
        <x-shop::shimmer.categories.view />
    </v-search>

    @if ($isAllProductsPage)
        <section class="bg-white">
            <div class="seo-read-more-block py-8 px-4 md:p-8">

                <h2 class="seo-main-heading">ANA Sports: Quality Cricket Bats and Cricket Sport Equipment in India
                </h2>

                <input type="checkbox" id="toggle-read-more" class="seo-toggle-checkbox" />

                <!-- SHORT PREVIEW (shown by default) -->
                <div class="seo-content-preview">
                    <p class="seo-text">In India, cricket is not played, it is lived. From the dusty streets and
                        school
                        grounds to well-kept academies and packed stadiums, it is a passion shared by millions.
                        Whether
                        the match is a friendly neighbourhood game or a high-pressure professional tournament,
                        premium
                        cricket sport equipment matters at every level. The cricket bat is where it begins. Pick the
                        wrong one and the game suffers. Pick the right one and everything clicks.</p>
                    <p class="seo-text">At ANA Sports, we cater to every kind of player, beginners still learning
                        the
                        basics, club players working to improve their game and professionals who demand the best. As
                        an
                        experienced cricket bat manufacturer, ANA Sports delivers bats built on strength, balance
                        and
                        superior performance. For players in India ready to buy cricket bats that match both their
                        technique and their budget, ANA Sports brings it all together with quality that never gets
                        compromised. Players can conveniently explore cricket bats online. They can choose from
                        different willow types, sizes, weights and price ranges.</p>
                </div>

                <!-- FULL CONTENT (hidden by default) -->
                <div class="seo-content-full">
                    <p class="seo-text">In India, cricket is not played, it is lived. From the dusty streets and
                        school
                        grounds to well-kept academies and packed stadiums, it is a passion shared by millions.
                        Whether
                        the match is a friendly neighbourhood game or a high-pressure professional tournament,
                        premium
                        cricket sport equipment matters at every level. The cricket bat is where it begins. Pick the
                        wrong one and the game suffers. Pick the right one and everything clicks.</p>
                    <p class="seo-text">At ANA Sports, we cater to every kind of player, beginners still learning
                        the
                        basics, club players working to improve their game and professionals who demand the best. As
                        an
                        experienced cricket bat manufacturer, ANA Sports delivers bats built on strength, balance
                        and
                        superior performance. For players in India ready to buy cricket bats that match both their
                        technique and their budget, ANA Sports brings it all together with quality that never gets
                        compromised. Players can conveniently explore cricket bats online. They can choose from
                        different willow types, sizes, weights and price ranges.</p>

                    <h3 class="seo-sub-heading">A Reliable Cricket Bat Store for Every Player</h3>
                    <p class="seo-text">A reliable cricket bat store is crucial when sourcing gear that endures and
                        excels on the field. ANA Sports provides an expert range of bats for all levels, from street
                        games to elite competition.</p>
                    <p class="seo-text">Players often search for a cricket bat near me because they want reliable,
                        fast
                        and accessible options. With our online catalog, ANA Sports enables customers in India to
                        browse
                        bats, compare specifications and select the ideal model from home.</p>
                    <p class="seo-text">Our selection includes:</p>
                    <ul class="seo-list">
                        <li class="seo-text"><a href="/beginner-and-training-bats" class="seo-link">Cricket bat for
                                beginners</a> designed with balanced weight and
                            forgiving sweet spots</li>
                        <li class="seo-text"><a href="/practice-series" class="seo-link">Practice
                                cricket bat</a> crafted for consistent performance during regular training sessions
                        </li>
                        <li class="seo-text">Professional cricket bat options designed for competitive players</li>
                        <li class="seo-text">Performance cricket bat models offering excellent stroke play and
                            control
                        </li>
                        <li class="seo-text">Lightweight youth bats are developed for young players to refine their
                            skills</li>
                    </ul>
                    <p class="seo-text">By offering multiple choices in one place, ANA Sports has become a preferred
                        destination for players looking to buy cricket bat that match their skill level and playing
                        style.</p>

                    <h3 class="seo-sub-heading">Cricket Bats Online for Convenience and Choice</h3>
                    <p class="seo-text">Today, many players like to purchase cricket bats online. This gives them
                        access
                        to a wider variety of products than most traditional stores. At <a href="/"
                            class="seo-link">ANA Sports</a>, our online store provides
                        detailed information about each bat. This helps players make informed decisions.</p>
                    <p class="seo-text">Customers can easily compare different features, like:</p>
                    <ul class="seo-list">
                        <li class="seo-text">Willow type</li>
                        <li class="seo-text">Bat profile</li>
                        <li class="seo-text">Weight range</li>
                        <li class="seo-text">Handle type</li>
                        <li class="seo-text">Blade thickness</li>
                        <li class="seo-text">Price range</li>
                    </ul>
                    <p class="seo-text">Whether you are searching for cricket bats for casual play or a <a
                            href="/professional-series" class="seo-link">professional cricket
                            bat</a> for matches, our online platform ensures a smooth and reliable shopping
                        experience.
                    </p>
                    <p class="seo-text">We know players value affordability when buying equipment. That is why ANA
                        Sports offers bats in a wide range of price points, making it easy for everyone to find an
                        option that suits their budget while still ensuring high quality.</p>

                    <h3 class="seo-sub-heading">Affordable Options for Every Budget</h3>
                    <p class="seo-text">Many newcomers and students seek affordable cricket bats that still offer
                        reliable performance. At ANA Sports, budget-friendly doesn't mean downgrading quality. Our
                        entry-level bats are meticulously crafted for longevity, balanced handling and consistent
                        hitting.</p>
                    <p class="seo-text">If you check the price of a cricket bat in India, it may vary. It depends on
                        the
                        type of willow, craftsmanship and brand reputation. ANA Sports offers transparent pricing
                        and
                        value-driven products. This lets players select the best bat within their budget.</p>
                    <p class="seo-text">Our collection includes bats suitable for:</p>
                    <ul class="seo-list">
                        <li class="seo-text">School cricket players</li>
                        <li class="seo-text">Weekend or recreational players</li>
                        <li class="seo-text">Club-level cricketers</li>
                        <li class="seo-text">Professional athletes</li>
                    </ul>
                    <p class="seo-text">This wide price range means every player can buy a cricket bat that fits
                        their
                        performance needs and budget.</p>

                    <h3 class="seo-sub-heading">Cricket Bat Manufacturers Focused on Performance</h3>
                    <p class="seo-text">As experienced cricket bat manufacturers, ANA Sports understands the
                        craftsmanship needed to produce high-quality bats. A cricket bat must offer power, control
                        and
                        durability. It should also maintain a proper balance.</p>
                    <p class="seo-text">Our bats are designed with careful attention to:</p>
                    <ul class="seo-list">
                        <li class="seo-text">Sweet spot positioning</li>
                        <li class="seo-text">Blade thickness and profile</li>
                        <li class="seo-text">Handle flexibility</li>
                        <li class="seo-text">Shock absorption</li>
                        <li class="seo-text">Weight distribution</li>
                    </ul>
                    <p class="seo-text">These factors create a <a href="/performance-series"
                            class="seo-link">performance cricket bat</a>, enabling players to hit powerful strokes
                        with
                        comfort and control and to reduce fatigue during long innings.</p>
                    <p class="seo-text">From young players practicing their first cover drive to seasoned cricketers
                        playing competitive matches, the right bat can boost confidence and improve performance.</p>

                    <h3 class="seo-sub-heading">Choosing the Right Bat for Your Playing Style</h3>
                    <p class="seo-text">Every player has a unique playing style. This is why picking the right
                        cricket
                        bat is important. A bat for a power hitter may not suit a technical stroke player.</p>
                    <p class="seo-text">When browsing cricket bats online, consider the following factors:</p>

                    <h4 class="seo-category-heading">Bat Weight</h4>
                    <p class="seo-text">Heavier bats provide power. Lighter bats allow quicker shots and give better
                        control.</p>

                    <h4 class="seo-category-heading">Blade Profile</h4>
                    <p class="seo-text">Different blade profiles affect the bat's sweet spot and shot-making
                        ability.
                    </p>

                    <h4 class="seo-category-heading">Handle Type</h4>
                    <p class="seo-text">The handle affects comfort, flexibility and shock absorption during impact.
                    </p>

                    <h4 class="seo-category-heading">Player Skill Level</h4>
                    <p class="seo-text">Beginners often benefit from bats with a bigger sweet spot and lighter
                        weight.
                    </p>

                    <h4 class="seo-category-heading">Age Group</h4>
                    <p class="seo-text">Young players should use properly sized <a href="/junior-and-youth-bats"
                            class="seo-link">youth bats</a>. This
                        helps them develop correct technique. If they consider these factors, players can
                        confidently
                        choose the right cricket bat. It works for both local stores and online.</p>

                    <h3 class="seo-sub-heading">Equipment Beyond Cricket Bats</h3>
                    <p class="seo-text">While bats are essential, full cricket equipment includes many other
                        important
                        items. ANA Sports supports players with a wide range of gear designed for safety and
                        performance.</p>
                    <p class="seo-text">Players often complement their bats with equipment like:</p>
                    <ul class="seo-list">
                        <li class="seo-text">Batting gloves</li>
                        <li class="seo-text">Batting pads</li>
                        <li class="seo-text">Helmets</li>
                        <li class="seo-text">Cricket balls</li>
                        <li class="seo-text">Protective guards</li>
                        <li class="seo-text">Training gear</li>
                    </ul>
                    <p class="seo-text">The right equipment helps players perform better. It also lowers the risk
                        of
                        injuries during practice or matches.</p>

                    <h3 class="seo-sub-heading">Supporting Young Cricketers and Beginners</h3>
                    <p class="seo-text">The journey of every great cricketer starts with good training and the
                        right
                        equipment. ANA Sports helps young players with lightweight cricket bats. We also offer
                        specially
                        designed youth bats.</p>
                    <p class="seo-text">Young players need bats that are easy to handle and support good batting
                        technique. Oversized or heavy bats can limit their performance and learning.</p>
                    <p class="seo-text">Our beginner-friendly bats help players focus on:</p>
                    <ul class="seo-list">
                        <li class="seo-text">Improving timing</li>
                        <li class="seo-text">Developing stroke technique</li>
                        <li class="seo-text">Building batting confidence</li>
                        <li class="seo-text">Practicing consistently</li>
                    </ul>
                    <p class="seo-text">With the right equipment, young cricketers can enjoy the game and steadily
                        improve their skills.</p>

                    <h3 class="seo-sub-heading">Why Do Players Trust ANA Sports?</h3>
                    <p class="seo-text">ANA Sports has built a reputation for reliable cricket equipment. We
                        balance
                        performance, durability and affordability. Players choose our products because they invest
                        in
                        equipment designed with real cricket needs in mind.</p>
                    <p class="seo-text">Key reasons customers prefer ANA Sports include:</p>
                    <ul class="seo-list">
                        <li class="seo-text">Wide range of cricket bats online</li>
                        <li class="seo-text">Affordable cricket bat price in India</li>
                        <li class="seo-text">Options for beginners, youth and professionals</li>
                        <li class="seo-text">Durable and well-crafted bats</li>
                        <li class="seo-text">Convenient online shopping experience</li>
                    </ul>
                    <p class="seo-text">Whether you want a practice bat, a professional bat or wish to find the
                        best
                        cricket bat store, ANA Sports aims to provide equipment that helps every cricketer perform
                        at
                        their best.</p>

                    <h3 class="seo-sub-heading">FAQs</h3>

                    <h4 class="seo-category-heading">What is a good price range for a cricket bat in India?</h4>
                    <p class="seo-text">A good cricket bat in India typically ranges from ₹1,500 to ₹15,000,
                        depending
                        on the willow type, craftsmanship and brand. Beginners usually choose affordable bats, while
                        professionals prefer premium English willow bats for better performance.</p>

                    <h4 class="seo-category-heading">How do you maintain and extend the life of a cheap cricket
                        bat?
                    </h4>
                    <p class="seo-text">To extend the life of cricket bats, apply linseed oil regularly. Store the
                        bat
                        in a dry place. Avoid moisture and use protective tape or a toe guard. Proper care keeps
                        bats
                        durable and performing well.</p>

                    <h4 class="seo-category-heading">Do cricket bats need to be knocked in before use?</h4>
                    <p class="seo-text">Yes, most cricket bats need to be knocked in before use. This process
                        compresses the willow fibers. It reduces the risk of cracks or damage when hitting hard
                        balls
                        and also improves durability and performance during matches.</p>

                    <h4 class="seo-category-heading">What types of bats are available for cricket?</h4>
                    <p class="seo-text">Cricket bats are available in several types, including beginner cricket
                        bats,
                        practice cricket bats, professional cricket bats, performance cricket bats and youth bats.
                        Each
                        type is designed to suit different skill levels, age groups and playing conditions.</p>

                    <h4 class="seo-category-heading">What is the difference between Kashmir Willow and English
                        Willow
                        cricket bats?</h4>
                    <p class="seo-text">Kashmir Willow bats are more affordable and durable, making them suitable
                        for
                        beginners. English Willow bats are lighter, provide better stroke performance and are
                        preferred
                        by professional players due to their superior grain quality.</p>

                    <h4 class="seo-category-heading">How important is grip comfort when choosing a cricket bat?
                    </h4>
                    <p class="seo-text">Grip comfort is important when selecting a cricket bat. A comfortable grip
                        improves control, reduces hand fatigue and helps players maintain better shot accuracy and
                        power
                        during long practice sessions or competitive matches.</p>

                    <h4 class="seo-category-heading">What should you look for when buying their first cricket bat?
                    </h4>
                    <p class="seo-text">When buying their first bat, players should consider weight, handle
                        comfort,
                        bat size, sweet spot and durability. Choosing a balanced cricket bat for beginners helps new
                        players develop proper batting technique and confidence.</p>

                    <h4 class="seo-category-heading">What is the ideal weight for a cricket bat?</h4>
                    <p class="seo-text">The ideal cricket bat weight usually ranges between 2.7 and 3 pounds.
                        Players
                        should choose a bat that feels comfortable to lift and swing, allowing good control without
                        sacrificing power on shots.</p>
                </div>
                <label for="toggle-read-more" class="seo-btn seo-btn-read-more">Read More</label>
                <label for="toggle-read-more" class="seo-btn seo-btn-read-less">Read Less</label>
            </div>
        </section>
    @endif

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-search-template"
        >
            <div class="bg-[#EDEDED] container px-[60px] max-lg:px-8 max-sm:px-4">
                <div class="flex items-start gap-10 max-lg:gap-5 py-10">
                    <!-- Product Listing Filters -->
                    @include('shop::categories.filters')

                    <!-- Product Listing Container -->
                    <div class="flex-1">
                        <!-- Desktop Product Listing Toolbar -->
                        <div class="max-md:hidden">
                            @include('shop::categories.toolbar')
                        </div>

                        <!-- Product List Card Container -->
                        <div
                            class="mt-8 grid grid-cols-1 gap-6"
                            v-if="(filters.toolbar.applied.mode ?? filters.toolbar.default.mode) === 'list'"
                        >
                            <!-- Product Card Shimmer Effect -->
                            <template v-if="isLoading">
                                <x-shop::shimmer.products.cards.list count="12" />
                            </template>

                            <!-- Product Card Listing -->
                            <template v-else>
                                <template v-if="products.length">
                                    <x-shop::products.card
                                        ::mode="'list'"
                                        v-for="product in products"
                                    />
                                </template>

                                <!-- Empty Products Container -->
                                <template v-else>
                                    <div class="m-auto grid w-full place-content-center items-center justify-items-center py-32 text-center">
                                        <img
                                            class="max-sm:h-[100px] max-sm:w-[100px]"
                                            src="{{ bagisto_asset('images/thank-you.png') }}"
                                            alt="Empty result"
                                            loading="lazy"
                                            decoding="async"
                                        />

                                        <p
                                            class="text-xl max-sm:text-sm"
                                            role="heading"
                                        >
                                            @lang('shop::app.categories.view.empty')
                                        </p>
                                    </div>
                                </template>
                            </template>
                        </div>

                        <!-- Product Grid Card Container -->
                        <div v-else>
                            <!-- Product Card Shimmer Effect -->
                            <template v-if="isLoading">
                                <div class="grid grid-cols-2 xl:grid-cols-3 gap-8 2xl:grid-cols-4 max-md:justify-items-center max-md:gap-x-4 mt-8 max-md:mt-5">
                                    <x-shop::shimmer.products.cards.grid count="12" />
                                </div>
                            </template>

                            <!-- Product Card Listing -->
                            <template v-else>
                                <template v-if="products.length">
                                    <div class="grid grid-cols-2 xl:grid-cols-3 gap-8 2xl:grid-cols-4 max-md:justify-items-center max-md:gap-x-4 mt-8 max-md:mt-5">
                                        <x-shop::products.card
                                            ::mode="'grid'"
                                            v-for="product in products"
                                            :navigation-link="route('shop.search.index')"
                                        />
                                    </div>
                                </template>

                                <!-- Empty Products Container -->
                                <template v-else>
                                    <div class="m-auto grid w-full place-content-center items-center justify-items-center py-32 text-center">
                                        <img
                                            class="max-sm:h-[100px] max-sm:w-[100px]"
                                            src="{{ bagisto_asset('images/thank-you.png') }}"
                                            alt="Empty result"
                                            loading="lazy"
                                            decoding="async"
                                        />

                                        <p
                                            class="text-xl max-sm:text-sm"
                                            role="heading"
                                        >
                                            @lang('shop::app.categories.view.empty')
                                        </p>
                                    </div>
                                </template>
                            </template>
                        </div>

                        <!-- Load More Button -->
                        <button
                            class="secondary-button mx-auto mt-[60px] block w-max rounded-2xl px-11 py-3 text-center text-base max-md:rounded-lg max-md:text-sm max-sm:mt-7 max-sm:px-7 max-sm:py-2"
                            @click="loadMoreProducts"
                            v-if="links.next"
                        >
                            @lang('shop::app.categories.view.load-more')
                        </button>
                    </div>
                </div>
            </div>
    </script>

        <script type="module">
            app.component('v-search', {
                template: '#v-search-template',

                data() {
                    return {
                        isMobile: window.innerWidth <= 767,

                        isLoading: true,

                        isDrawerActive: {
                            toolbar: false,

                            filter: false,
                        },

                        filters: {
                            toolbar: {
                                default: {},

                                applied: {},
                            },

                            filter: {},
                        },

                        products: [],

                        links: {},
                    }
                },

                computed: {
                    queryParams() {
                        let queryParams = Object.assign({}, this.filters.filter, this.filters.toolbar.applied);

                        return this.removeJsonEmptyValues(queryParams);
                    },

                    queryString() {
                        return this.jsonToQueryString(this.queryParams);
                    },
                },

                watch: {
                    queryParams() {
                        this.getProducts();
                    },

                    queryString() {
                        window.history.pushState({}, '', '?' + this.queryString);
                    },
                },

                methods: {
                    setFilters(type, filters) {
                        this.filters[type] = filters;
                    },

                    clearFilters(type, filters) {
                        this.filters[type] = {};
                    },

                    getProducts() {
                        this.isDrawerActive = {
                            toolbar: false,

                            filter: false,
                        };

                        this.$axios.get(("{{ route('shop.api.products.index') }}"), {
                                params: this.queryParams
                            })
                            .then(response => {
                                this.isLoading = false;

                                this.products = response.data.data;

                                this.links = response.data.links;
                            }).catch(error => {
                                console.log(error);
                            });
                    },

                    loadMoreProducts() {
                        if (this.links.next) {
                            this.$axios.get(this.links.next).then(response => {
                                this.products = [...this.products, ...response.data.data];

                                this.links = response.data.links;
                            }).catch(error => {
                                console.log(error);
                            });
                        }
                    },

                    removeJsonEmptyValues(params) {
                        Object.keys(params).forEach(function(key) {
                            if ((!params[key] && params[key] !== undefined)) {
                                delete params[key];
                            }

                            if (Array.isArray(params[key])) {
                                params[key] = params[key].join(',');
                            }
                        });

                        return params;
                    },

                    jsonToQueryString(params) {
                        let parameters = new URLSearchParams();

                        for (const key in params) {
                            parameters.append(key, params[key]);
                        }

                        return parameters.toString();
                    }
                },
            });
        </script>
    @endPushOnce
</x-shop::layouts>
