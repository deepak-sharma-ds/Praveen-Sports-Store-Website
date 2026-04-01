<v-product-gallery ref="gallery">
    <x-shop::shimmer.products.gallery />
</v-product-gallery>

{{-- Import map for Three.js bare specifier resolution (must render before any <script type="module">) --}}
@pushOnce('meta')
    <script type="importmap">
        {
            "imports": {
                "three": "https://cdn.jsdelivr.net/npm/three@0.157.0/build/three.module.js"
            }
        }
    </script>
@endPushOnce

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-product-gallery-template"
    >
        <div class="w-full md:w-1/2">
            <!-- Gallery Tab Navigation -->
            <div class="bat-gallery-tabs flex gap-2 mb-4">
                <button
                    type="button"
                    class="gallery-tab px-5 py-2.5 rounded-full text-sm font-medium transition-all duration-200 border"
                    :class="galleryActiveTab === 'images'
                        ? 'bg-[#902129] text-white border-[#902129]'
                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                    @click="galleryActiveTab = 'images'"
                >
                    &#128247; Images
                </button>

                <button
                    type="button"
                    class="gallery-tab px-5 py-2.5 rounded-full text-sm font-medium transition-all duration-200 border"
                    :class="galleryActiveTab === '3d'
                        ? 'bg-[#902129] text-white border-[#902129]'
                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                    @click="activate3DTab()"
                >
                    &#127919; 3D View
                </button>
            </div>

            <!-- Panel A: Images (existing gallery — unchanged) -->
            <div v-show="galleryActiveTab === 'images'">
                <!-- Desktop Gallery -->
                @include('shop::products.view.gallery.desktop')

                <!-- Mobile Gallery -->
                @include('shop::products.view.gallery.mobile')

                <!-- Gallery Images Zoomer -->
                <x-shop::image-zoomer
                    ::attachments="attachments"
                    ::is-image-zooming="isImageZooming"
                    ::initial-index="`media_${activeIndex}`"
                />
            </div>

            <!-- Panel B: 3D View -->
            <div v-show="galleryActiveTab === '3d'">
                <!-- 3D wrapper: relative so the loading overlay sits on top of the always-laid-out canvas -->
                <div class="relative" style="width: 100%; height: 480px;">

                    <!-- 3D Canvas Container — ALWAYS in the DOM with layout so Three.js reads real dimensions -->
                    <div
                        id="bat-3d-canvas-container"
                        class="absolute inset-0 rounded-2xl bg-[#F8F5F0] overflow-hidden"
                    ></div>

                    <!-- Loading Overlay (sits on top of the canvas, removed after load) -->
                    <div
                        id="bat-3d-loading"
                        class="absolute inset-0 z-10 flex flex-col items-center justify-center rounded-2xl bg-[#F8F5F0]"
                        v-show="bat3dLoading"
                    >
                        <div class="bat-3d-spinner mb-4"></div>
                        <p class="text-sm text-gray-500">
                            Loading 3D Model… <span id="bat-3d-loading-percent">0%</span>
                        </p>
                    </div>

                    <!-- WebGL Fallback -->
                    <div
                        id="bat-3d-webgl-fallback"
                        class="absolute inset-0 z-10 flex-col items-center justify-center rounded-2xl bg-[#F8F5F0] p-8 text-center"
                        style="display: none;"
                    >
                        <svg class="mx-auto mb-4 h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <p class="text-gray-600 font-medium">3D View Unavailable</p>
                        <p class="text-sm text-gray-400 mt-1">Your browser does not support WebGL. Please use the Images tab.</p>
                    </div>

                </div>

                <!-- Rotation Hint -->
                <p
                    class="mt-2 text-center text-xs text-gray-400"
                    v-show="!bat3dLoading && galleryActiveTab === '3d'"
                >
                    &#128433; Drag to rotate &middot; Scroll to zoom
                </p>
            </div>
        </div>
    </script>

    <script type="module" src="{{ asset('js/bat-3d-configurator.js') }}"></script>

    <script type="module">
        app.component('v-product-gallery', {
            template: '#v-product-gallery-template',

            data() {
                return {
                    isImageZooming: false,

                    isMediaLoading: true,

                    media: {
                        images: @json(product_image()->getGalleryImages($product)),

                        videos: @json(product_video()->getVideos($product)),
                    },

                    baseFile: {
                        type: '',

                        path: ''
                    },

                    activeIndex: 0,

                    containerOffset: 110,

                    galleryActiveTab: 'images',

                    bat3dLoading: true,

                    bat3dInitialized: false,
                };
            },

            watch: {
                'media.images': {
                    deep: true,

                    handler(newImages, oldImages) {
                        let selectedImage = newImages?.[this.activeIndex];

                        if (JSON.stringify(newImages) !== JSON.stringify(oldImages) && selectedImage
                            ?.large_image_url) {
                            this.baseFile.path = selectedImage.large_image_url;
                        }
                    },
                },
            },

            mounted() {
                if (this.media.images.length) {

                    this.baseFile.type = 'image';

                    this.baseFile.path = this.media.images[0].large_image_url;
                } else if (this.media.videos.length) {

                    this.baseFile.type = 'video';

                    this.baseFile.path = this.media.videos[0].video_url;
                }
            },

            computed: {
                lengthOfMedia() {
                    if (this.media.images.length) {
                        return [...this.media.images, ...this.media.videos].length > 5;
                    }
                },

                attachments() {
                    return [...this.media.images, ...this.media.videos].map(media => ({
                        url: media.type === 'videos' ? media.video_url : media.original_image_url,

                        type: media.type === 'videos' ? 'video' : 'image',
                    }));
                },
            },

            methods: {
                async activate3DTab() {
                    this.galleryActiveTab = '3d';

                    if (this.bat3dInitialized) return;
                    this.bat3dInitialized = true;

                    /* First $nextTick: Vue applies v-show (Panel B becomes display:block)
                       Second $nextTick: browser has completed layout — container has real dimensions */
                    await this.$nextTick();
                    await this.$nextTick();

                    const loaded = await this.waitForConfigurator();

                    if (!loaded) {
                        this.bat3dLoading = false;
                        return;
                    }

                    const configurator = new window.BatConfigurator('bat-3d-canvas-container');
                    window._batConfiguratorInstance = configurator;

                    try {
                        await configurator.init();

                        if (configurator.renderer) {
                            await configurator.loadModel("{{ asset('3d-models/cricket-bat.glb') }}");

                            /* Safety: force resize after model load in case layout shifted */
                            configurator.onResize();

                            /* Apply the current combination-image texture if the
                               user selected options before opening the 3D tab.
                               The options component stores the resolved .webp path
                               on window._batCurrentTexturePath via its watcher. */
                            if (window._batCurrentTexturePath) {
                                configurator.applyTexture('Bat_Body', window._batCurrentTexturePath);
                            }
                        }
                    } catch (err) {
                        console.error('[Gallery] 3D init error:', err);
                    }

                    this.bat3dLoading = false;
                },

                async waitForConfigurator(timeout = 5000) {
                    const start = Date.now();

                    while (!window.BatConfigurator) {
                        if (Date.now() - start > timeout) {
                            console.error('[Gallery] BatConfigurator load timeout');
                            return false;
                        }
                        await new Promise(r => setTimeout(r, 50));
                    }

                    return true;
                },

                isActiveMedia(index) {
                    return index === this.activeIndex;
                },

                onMediaLoad() {
                    this.isMediaLoading = false;
                },

                change(media, index) {
                    this.isMediaLoading = true;

                    if (media.type == 'videos') {
                        this.baseFile.type = 'video';

                        this.baseFile.path = media.video_url;

                        this.onMediaLoad();
                    } else {
                        this.baseFile.type = 'image';

                        this.baseFile.path = media.large_image_url;
                    }

                    if (index > this.activeIndex) {
                        this.swipeDown();
                    } else if (index < this.activeIndex) {
                        this.swipeTop();
                    }

                    this.activeIndex = index;
                },

                swipeTop() {
                    const container = this.$refs.swiperContainer;

                    container.scrollTop -= this.containerOffset;
                },

                swipeDown() {
                    const container = this.$refs.swiperContainer;

                    container.scrollTop += this.containerOffset;
                },
            },
        });
    </script>
@endpushOnce
