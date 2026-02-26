<v-desktop-tabs></v-desktop-tabs>

@pushOnce('scripts')
    <script type="text/x-template" id="v-desktop-tabs-template">
        <div class="flex items-center gap-3 xl:gap-8 font-medium text-black uppercase tracking-widest">

            <a href="{{ route('shop.home.index') }}"
               class="hover:text-primary transition"
               :class="{ 'text-primary font-semibold': activeTab === 'home' }">
                Home
            </a>

             <div
                class="relative"
                ref="categoryDropdown"
                @mouseenter="openCategoryDropdown"
                @mouseleave="closeCategoryDropdown"
            >
                <button
                    type="button"
                    class="hover:text-primary transition flex items-center gap-2 uppercase"
                    :class="{ 'text-primary font-semibold': isCategoryDropdownOpen }"
                    @click.stop="toggleCategoryDropdown"
                >
                    Collections

                    <span
                        class="text-xs transition-transform duration-200"
                        :class="{ 'rotate-180': isCategoryDropdownOpen }"
                    >
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                            <path d="M297.4 470.6C309.9 483.1 330.2 483.1 342.7 470.6L534.7 278.6C547.2 266.1 547.2 245.8 534.7 233.3C522.2 220.8 501.9 220.8 489.4 233.3L320 402.7L150.6 233.4C138.1 220.9 117.8 220.9 105.3 233.4C92.8 245.9 92.8 266.2 105.3 278.7L297.3 470.7z"/>
                        </svg>
                    </span>
                </button>

                <div
                    v-show="isCategoryDropdownOpen"
                    class="absolute left-0 top-full -mt-1 z-20 min-w-[320px] max-h-[420px] overflow-auto rounded-md border border-zinc-200 bg-white py-3 shadow-lg normal-case tracking-normal"
                >
                    <div
                        v-if="isLoading"
                        class="px-4 py-2 text-sm text-zinc-500"
                    >
                        Loading categories...
                    </div>

                    <template v-else>
                        <div
                            class="px-4 py-2"
                        >
                            <p class="text-sm font-semibold text-black">
                                Series
                            </p>

                            <ul class="mt-1 list-disc pl-5">
                                <li
                                    v-for="seriesCategory in categories"
                                    :key="seriesCategory.id"
                                    class="py-1 text-sm"
                                >
                                    <a
                                        :href="seriesCategory.url"
                                        class="text-zinc-700 hover:text-primary"
                                        :class="{ 'font-semibold text-primary': isCategoryActive(seriesCategory.url) }"
                                        @click="closeDropdownOnSelection"
                                    >
                                        @{{ seriesCategory.name }}
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div
                            v-if="featuredCollections.length"
                            class="px-4 py-2"
                        >
                            <p class="text-sm font-semibold text-black">
                                Featured Collections
                            </p>

                            <ul class="mt-1 list-disc pl-5">
                                <li
                                    v-for="(featuredCategory, featuredIndex) in featuredCollections"
                                    :key="`${featuredCategory.id}-${featuredIndex}`"
                                    class="py-1 text-sm"
                                >
                                    <a
                                        :href="featuredCategory.url"
                                        class="text-zinc-700 hover:text-primary"
                                        :class="{ 'font-semibold text-primary': isCategoryActive(featuredCategory.url) }"
                                        @click="closeDropdownOnSelection"
                                    >
                                        @{{ featuredCategory.name }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </template>
                </div>
            </div>

            <a href="{{ route('shop.all.product.index', ['sort' => 'name-desc']) }}"
               class="hover:text-primary transition"
               :class="{ 'text-primary font-semibold': activeTab === 'products' }">
                Our Products
            </a>

            <a href="/blog"
               class="hover:text-primary transition"
               :class="{ 'text-primary font-semibold': activeTab === 'blog' }">
                Blog
            </a>

            <a href="{{ route('shop.cms.page', 'about-us') }}"
               class="hover:text-primary transition"
               :class="{ 'text-primary font-semibold': activeTab === 'about' }">
                About Us
            </a>
        </div>
    </script>

    <script type="module">
        app.component('v-desktop-tabs', {
            template: '#v-desktop-tabs-template',

            data() {
                return {
                    activeTab: this.getActiveTab(),
                    isLoading: true,
                    categories: [],
                    isCategoryDropdownOpen: false,
                    isCategoryDropdownPinned: false,
                    currentPath: '/',
                };
            },

            mounted() {
                this.initCategories();
                this.currentPath = this.normalizePath(window.location.pathname);
                document.addEventListener('click', this.handleOutsideClick);
            },

            beforeUnmount() {
                document.removeEventListener('click', this.handleOutsideClick);
            },

            computed: {
                featuredCollections() {
                    return this.categories.reduce((result, category) => {
                        if (category.children?.length) {
                            result.push(...category.children);
                        }

                        return result;
                    }, []);
                },
            },

            methods: {
                getActiveTab() {
                    const path = window.location.pathname;

                    if (path === '/' || path === '/home') return 'home';
                    if (path.startsWith('/search')) return 'products';
                    if (path.startsWith('/blog')) return 'blog';
                    if (path.startsWith('/about-us')) return 'about';

                    return '';
                },

                initCategories() {
                    try {
                        const stored = localStorage.getItem('categories');

                        if (stored) {
                            this.categories = JSON.parse(stored);
                            this.isLoading = false;

                            return;
                        }
                    } catch (e) {}

                    this.getCategories();
                },

                getCategories() {
                    this.$axios.get("{{ route('shop.api.categories.tree') }}")
                        .then((response) => {
                            this.categories = response.data.data;
                            this.isLoading = false;
                            localStorage.setItem('categories', JSON.stringify(this.categories));
                        })
                        .catch(() => {
                            this.isLoading = false;
                        });
                },

                openCategoryDropdown() {
                    this.isCategoryDropdownOpen = true;
                },

                closeCategoryDropdown() {
                    if (this.isCategoryDropdownPinned) {
                        return;
                    }

                    this.isCategoryDropdownOpen = false;
                },

                toggleCategoryDropdown() {
                    this.isCategoryDropdownPinned = !this.isCategoryDropdownPinned;
                    this.isCategoryDropdownOpen = this.isCategoryDropdownPinned;
                },

                handleOutsideClick(event) {
                    if (!this.$refs.categoryDropdown?.contains(event.target)) {
                        this.isCategoryDropdownPinned = false;
                        this.isCategoryDropdownOpen = false;
                    }
                },

                closeDropdownOnSelection() {
                    this.isCategoryDropdownPinned = false;
                    this.isCategoryDropdownOpen = false;
                },

                normalizePath(path) {
                    const normalizedPath = (path || '/').replace(/\/+$/, '');

                    return normalizedPath || '/';
                },

                isCategoryActive(url) {
                    try {
                        const categoryPath = this.normalizePath(new URL(url, window.location.origin).pathname);

                        return this.currentPath === categoryPath;
                    } catch (e) {
                        return false;
                    }
                },
            },
        });
    </script>
@endPushOnce
