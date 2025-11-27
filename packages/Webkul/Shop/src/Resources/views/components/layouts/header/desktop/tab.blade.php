<v-desktop-tabs></v-desktop-tabs>

@pushOnce('scripts')
    <script type="text/x-template" id="v-desktop-tabs-template">
        <div class="flex items-center gap-3 xl:gap-8 font-medium text-black uppercase tracking-widest">
            <a href="{{ route('shop.home.index') }}"
               class="hover:text-primary transition"
               :class="{ 'text-primary font-semibold': activeTab === 'home' }">
                Home
            </a>

            <a href="{{ route('shop.search.index', ['sort' => 'name-desc']) }}"
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
                };
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
            },
        });
    </script>
@endPushOnce
