{{-- 360° Viewer Modal (rendered server-side, outside Vue component) --}}
@if(isset($product360Images) && count($product360Images) >= 2)
    {{-- Modal Structure --}}
    <div id="product360Modal" class="product-360-modal" style="display: none;">
        <div class="product-360-modal-overlay" onclick="closeProduct360Modal()"></div>
        <div class="product-360-modal-container">

            {{-- Header --}}
            <div class="product-360-modal-header">
                <div class="product-360-modal-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    360° Product View
                </div>
                <button type="button" class="product-360-modal-close" onclick="closeProduct360Modal()" aria-label="Close 360° viewer">
                    <span class="icon-cancel"></span>
                </button>
            </div>

            {{-- Viewer --}}
            <div id="product360ViewerContainer" class="product-360-viewer-wrapper"></div>

            {{-- Footer Controls --}}
            <div class="product-360-modal-footer">
                <p class="product-360-footer-hint">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11" />
                    </svg>
                    Drag to rotate
                </p>
                <div class="product-360-footer-actions">
                    <button type="button" class="product-360-ctrl-btn" onclick="rotate360Prev()" title="Previous frame">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        Prev
                    </button>
                    <button type="button" class="product-360-ctrl-btn product-360-ctrl-btn--primary" id="btn360AutoRotate" onclick="toggle360AutoRotate()" title="Toggle auto-rotation">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Auto Rotate
                    </button>
                    <button type="button" class="product-360-ctrl-btn" onclick="rotate360Next()" title="Next frame">
                        Next
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

        </div>
    </div>

    {{-- Modal Control JavaScript --}}
    @pushOnce('scripts')
        <script>
            let product360ViewerInstance = null;
            let product360AutoRotating = false;

            function openProduct360Modal() {
                const modal = document.getElementById('product360Modal');
                const container = document.getElementById('product360ViewerContainer');

                if (modal && container) {
                    modal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';

                    if (!product360ViewerInstance && window.Product360Viewer) {
                        const images = @json($product360Images);
                        const config = @json($product360Config ?? []);
                        product360ViewerInstance = new Product360Viewer(container, images, config);
                    }
                }
            }

            function closeProduct360Modal() {
                const modal = document.getElementById('product360Modal');
                if (modal) {
                    modal.style.display = 'none';
                    document.body.style.overflow = '';
                }
                // Stop auto-rotate and reset button state
                if (product360ViewerInstance && product360AutoRotating) {
                    product360ViewerInstance.stopAutoRotate();
                    product360AutoRotating = false;
                    const btn = document.getElementById('btn360AutoRotate');
                    if (btn) btn.classList.remove('active');
                }
            }

            function toggle360AutoRotate() {
                if (!product360ViewerInstance) return;
                product360AutoRotating = !product360AutoRotating;
                const btn = document.getElementById('btn360AutoRotate');
                if (product360AutoRotating) {
                    product360ViewerInstance.startAutoRotate();
                    if (btn) btn.classList.add('active');
                } else {
                    product360ViewerInstance.stopAutoRotate();
                    if (btn) btn.classList.remove('active');
                }
            }

            function rotate360Prev() {
                if (!product360ViewerInstance) return;
                const total = product360ViewerInstance.images.length;
                const prev = ((product360ViewerInstance.currentIndex - 1) + total) % total;
                product360ViewerInstance.updateImage(prev);
            }

            function rotate360Next() {
                if (!product360ViewerInstance) return;
                const total = product360ViewerInstance.images.length;
                const next = (product360ViewerInstance.currentIndex + 1) % total;
                product360ViewerInstance.updateImage(next);
            }

            // Close modal on Escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeProduct360Modal();
                }
            });
        </script>
    @endPushOnce

    {{-- Load Product360Viewer Assets --}}
    @pushOnce('styles')
        <link rel="stylesheet" href="{{ asset('vendor/product360/css/product360-viewer.css') }}">
    @endPushOnce

    @pushOnce('scripts')
        <script src="{{ asset('vendor/product360/js/product360-viewer.js') }}"></script>
    @endPushOnce
@endif

<v-product-gallery ref="gallery">
    <x-shop::shimmer.products.gallery />
</v-product-gallery>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-product-gallery-template"
    >
        <div class="w-full md:w-1/2">
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
    </script>

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
                };
            },

            watch: {
                'media.images': {
                    deep: true,

                    handler(newImages, oldImages) {
                        let selectedImage = newImages?.[this.activeIndex];

                        if (JSON.stringify(newImages) !== JSON.stringify(oldImages) && selectedImage?.large_image_url) {
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
