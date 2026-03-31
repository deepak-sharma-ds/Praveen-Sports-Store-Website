<!-- For large screens greater than 1239px. -->
<div class="sticky top-20 flex-col-reverse h-max gap-8 hidden lg:flex">
    <!-- Product Image and Videos Slider -->
    <div class="w-full flex place-content-start justify-center gap-2.5 overflow-x-auto overflow-y-hidden">
        <!-- Arrow Up -->
        <span
            class="icon-arrow-up cursor-pointer text-2xl"
            role="button"
            aria-label="@lang('shop::app.components.products.carousel.previous')"
            tabindex="0"
            @click="swipeDown"
            v-if="lengthOfMedia"
        >
        </span>

        <!-- Swiper Container -->
        <div
            ref="swiperContainer"
            class="flex flex-row w-full gap-2.5 [&>*]:flex-[0] overflow-auto scroll-smooth scrollbar-hide"
        >
            <template v-for="(media, index) in [...media.images, ...media.videos]">
                <video
                    v-if="media.type == 'videos'"
                    :class="`transparent max-h-[100px] min-w-[100px] cursor-pointer rounded-xl border ${isActiveMedia(index) ? 'pointer-events-none border-navyBlue' : 'border-white'}`"
                    @click="change(media, index)"
                    alt="{{ $product->name }}"
                    tabindex="0"
                >
                    <source
                        :src="media.video_url"
                        type="video/mp4"
                    />
                </video>

                <img
                    v-else
                    :class="`transparent max-h-[100px] min-w-[100px] cursor-pointer rounded-xl border ${isActiveMedia(index) ? 'pointer-events-none border border-navyBlue' : 'border-white'}`"
                    :src="media.small_image_url"
                    alt="{{ $product->name }}"
                    width="100"
                    height="100"
                    tabindex="0"
                    @click="change(media, index)"
                />
            </template>

            {{-- 360° Thumbnail Button --}}
            @if(isset($product360Images) && count($product360Images) >= 2)
                <button
                    type="button"
                    class="transparent max-h-[100px] min-w-[100px] cursor-pointer rounded-xl border border-white bg-gray-100 hover:border-navyBlue flex items-center justify-center"
                    onclick="openProduct360Modal()"
                    aria-label="View 360° rotation"
                    tabindex="0"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-navyBlue" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span class="sr-only">360° View</span>
                </button>
            @endif
        </div>

        <!-- Arrow Down -->
        <span
            class="icon-arrow-down cursor-pointer text-2xl"
            v-if= "lengthOfMedia"
            role="button"
            aria-label="@lang('shop::app.components.products.carousel.previous')"
            tabindex="0"
            @click="swipeTop"
        >
        </span>
    </div>

    <!-- Product Base Image and Video with Shimmer-->
    <div
        class="max-h-[610px] max-w-[700px]"
        v-show="isMediaLoading"
    >
        <div class="shimmer min-h-[607px] min-w-[560px] rounded-xl bg-zinc-200"></div>
    </div>

    <div
        class="w-full"
        v-show="! isMediaLoading"
    >
        <img
            class="w-full aspect-square object-contain bg-white cursor-pointer rounded-xl"
            :src="baseFile.path"
            v-if="baseFile.type == 'image'"
            alt="{{ $product->name }}"
            width="560"
            height="610"
            tabindex="0"
            @click="isImageZooming = !isImageZooming"
            @load="onMediaLoad()"
            fetchpriority="high"
        />

        <div
            class="w-full cursor-pointer rounded-xl"
            tabindex="0"
            v-if="baseFile.type == 'video'"
        >
            <video
                controls
                width="475"
                alt="{{ $product->name }}"
                @click="isImageZooming = !isImageZooming"
                @loadeddata="onMediaLoad()"
                :key="baseFile.path"
            >
                <source
                    :src="baseFile.path"
                    type="video/mp4"
                />
            </video>
        </div>
    </div>
</div>
