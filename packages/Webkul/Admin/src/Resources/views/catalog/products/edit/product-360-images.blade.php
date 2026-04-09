{!! view_render_event('bagisto.admin.catalog.product.edit.form.product_360_images.before', ['product' => $product]) !!}

<div class="box-shadow relative rounded bg-white p-4 dark:bg-gray-900">
    <!-- Panel Header -->
    <div class="mb-4 flex justify-between gap-5">
        <div class="flex flex-col gap-2">
            <p class="text-base font-semibold text-gray-800 dark:text-white">
                360° View Images
            </p>

            <p class="text-xs font-medium text-gray-500 dark:text-gray-300">
                Upload images in sequence for 360° rotation. Minimum 2 images required. Drag to reorder.
            </p>
        </div>
    </div>

    @php
        $uploadedImages = $product->product360Images()
            ->orderBy('position')
            ->get()
            ->map(function ($img) {
                return [
                    'id'       => $img->id,
                    'url'      => $img->url,
                    'path'     => $img->path,
                    'position' => $img->position,
                ];
            });
    @endphp

    <v-product360-images
        :product-id="{{ $product->id }}"
        :uploaded-images='@json($uploadedImages)'
    ></v-product360-images>
</div>

{!! view_render_event('bagisto.admin.catalog.product.edit.form.product_360_images.after', ['product' => $product]) !!}

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-product360-images-template"
    >
        <div class="grid gap-4">
            <!-- Upload Section -->
            <div class="flex flex-wrap gap-1">
                <!-- Upload Button -->
                <label
                    class="grid h-[120px] max-h-[120px] min-h-[110px] w-full min-w-[110px] max-w-[120px] cursor-pointer items-center justify-items-center rounded border border-dashed border-gray-300 transition-all hover:border-gray-400 dark:border-gray-800 dark:mix-blend-exclusion dark:invert"
                    :class="uploadError ? 'border-red-500' : 'border-gray-300'"
                    :for="$.uid + '_product360ImageInput'"
                >
                    <div class="flex flex-col items-center">
                        <span class="icon-image text-2xl"></span>

                        <p class="grid text-center text-xs font-semibold text-gray-600 dark:text-gray-300">
                            Add 360° Images

                            <span class="text-[10px]">
                                JPEG, PNG, WebP (max 5MB)
                            </span>
                        </p>

                        <input
                            type="file"
                            class="hidden"
                            :id="$.uid + '_product360ImageInput'"
                            accept="image/jpeg,image/jpg,image/png,image/webp"
                            multiple
                            @change="handleFileSelect"
                        />
                    </div>
                </label>

                <!-- Uploaded Images (Draggable) -->
                <draggable
                    class="flex flex-wrap gap-1"
                    ghost-class="draggable-ghost"
                    v-bind="{animation: 200}"
                    :list="images"
                    item-key="id"
                    @end="handleReorder"
                >
                    <template #item="{ element, index }">
                        <div class="group relative grid h-[120px] w-[120px] max-h-[120px] min-w-[110px] max-w-[120px] cursor-move justify-items-center overflow-hidden rounded border border-gray-300 transition-all hover:border-gray-400 dark:border-gray-800">
                            <!-- Image Preview -->
                            <img
                                :src="element.url"
                                class="h-full w-full object-cover"
                                :alt="'360 image ' + (index + 1)"
                            />

                            <!-- Position Badge -->
                            <div class="absolute left-1 top-1 rounded bg-gray-800 bg-opacity-70 px-1.5 py-0.5 text-[10px] font-semibold text-white">
                                @{{ index + 1 }}
                            </div>

                            <!-- Hover Actions -->
                            <div class="invisible absolute bottom-0 top-0 flex w-full flex-col justify-end bg-white p-2 opacity-90 transition-all group-hover:visible dark:bg-gray-900">
                                <div class="flex justify-center gap-2">
                                    <span
                                        class="icon-delete cursor-pointer rounded-md p-1.5 text-2xl text-red-600 hover:bg-gray-200 dark:hover:bg-gray-800"
                                        @click="handleDelete(element, index)"
                                        title="Delete image"
                                    ></span>
                                </div>
                            </div>

                            <!-- Upload Progress -->
                            <div
                                v-if="element.uploading"
                                class="absolute bottom-0 left-0 right-0 top-0 flex items-center justify-center bg-gray-900 bg-opacity-70"
                            >
                                <div class="text-center">
                                    <img
                                        class="mx-auto h-8 w-8 animate-spin"
                                        src="{{ bagisto_asset('images/spinner.svg') }}"
                                    />
                                    <p class="mt-2 text-xs text-white">@{{ element.progress }}%</p>
                                </div>
                            </div>
                        </div>
                    </template>
                </draggable>
            </div>

            <!-- Error Message -->
            <div v-if="uploadError" class="rounded border border-red-300 bg-red-50 p-3 text-sm text-red-600 dark:bg-red-900 dark:text-red-200">
                @{{ uploadError }}
            </div>

            <!-- Success Message -->
            <div v-if="successMessage" class="rounded border border-green-300 bg-green-50 p-3 text-sm text-green-600 dark:bg-green-900 dark:text-green-200">
                @{{ successMessage }}
            </div>

            <!-- Info Messages -->
            <div v-if="images.length === 0" class="rounded border border-blue-300 bg-blue-50 p-3 text-sm text-blue-600 dark:bg-blue-900 dark:text-blue-200">
                No 360° images uploaded yet. Upload at least 2 images to enable the 360° viewer on the product page.
            </div>

            <div v-else-if="images.length === 1" class="rounded border border-yellow-300 bg-yellow-50 p-3 text-sm text-yellow-600 dark:bg-yellow-900 dark:text-yellow-200">
                Upload at least one more image to enable the 360° viewer (minimum 2 images required).
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-product360-images', {
            template: '#v-product360-images-template',

            props: {
                productId: {
                    type: Number,
                    required: true,
                },

                uploadedImages: {
                    type: Array,
                    default: () => [],
                },
            },

            data() {
                return {
                    images: [],
                    uploadError: null,
                    successMessage: null,
                    isUploading: false,
                };
            },

            mounted() {
                this.loadImages();
            },

            methods: {
                loadImages() {
                    this.images = this.uploadedImages.map(img => ({
                        id: img.id,
                        url: img.url || img.path,
                        position: img.position,
                        uploading: false,
                        progress: 0,
                    }));
                },

                handleFileSelect(event) {
                    const files = Array.from(event.target.files);

                    if (files.length === 0) return;

                    this.uploadError = null;
                    this.successMessage = null;

                    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                    const maxSize   = 5 * 1024 * 1024;

                    for (let file of files) {
                        if (! validTypes.includes(file.type)) {
                            this.uploadError = `Invalid file type: ${file.name}. Only JPEG, PNG, and WebP are allowed.`;
                            event.target.value = '';
                            return;
                        }

                        if (file.size > maxSize) {
                            this.uploadError = `File too large: ${file.name}. Maximum size is 5MB.`;
                            event.target.value = '';
                            return;
                        }
                    }

                    this.uploadFiles(files);
                    event.target.value = '';
                },

                async uploadFiles(files) {
                    this.isUploading = true;

                    const formData   = new FormData();
                    const tempImages = [];

                    files.forEach(file => formData.append('images[]', file));

                    for (let file of files) {
                        const tempId    = 'temp_' + Date.now() + '_' + Math.random();
                        const tempImage = { id: tempId, url: URL.createObjectURL(file), uploading: true, progress: 0 };
                        tempImages.push(tempImage);
                        this.images.push(tempImage);
                    }

                    try {
                        const response = await this.$axios.post(
                            `/admin/catalog/products/${this.productId}/360-images/upload`,
                            formData,
                            {
                                headers: { 'Content-Type': 'multipart/form-data' },
                                onUploadProgress: (progressEvent) => {
                                    const progress = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                                    tempImages.forEach(img => { img.progress = progress; });
                                },
                            }
                        );

                        tempImages.forEach(tempImg => {
                            const idx = this.images.findIndex(img => img.id === tempImg.id);
                            if (idx !== -1) this.images.splice(idx, 1);
                        });

                        if (response.data.success && response.data.data.images) {
                            response.data.data.images.forEach(img => {
                                this.images.push({ id: img.id, url: img.url, position: img.position, uploading: false, progress: 0 });
                            });

                            this.successMessage = `Successfully uploaded ${files.length} image(s)`;
                            setTimeout(() => { this.successMessage = null; }, 3000);
                        }
                    } catch (error) {
                        tempImages.forEach(tempImg => {
                            const idx = this.images.findIndex(img => img.id === tempImg.id);
                            if (idx !== -1) this.images.splice(idx, 1);
                        });

                        if (error.response?.data?.errors) {
                            this.uploadError = Object.values(error.response.data.errors).flat().join(' ');
                        } else {
                            this.uploadError = error.response?.data?.message || 'Upload failed. Please try again.';
                        }
                    } finally {
                        this.isUploading = false;
                    }
                },

                async handleReorder() {
                    const order = this.images.map((img, index) => ({ id: img.id, position: index + 1 }));

                    try {
                        const response = await this.$axios.put(
                            `/admin/catalog/products/${this.productId}/360-images/reorder`,
                            { order }
                        );

                        if (response.data.success) {
                            this.images.forEach((img, index) => { img.position = index + 1; });
                            this.successMessage = 'Images reordered successfully';
                            setTimeout(() => { this.successMessage = null; }, 2000);
                        }
                    } catch (error) {
                        this.uploadError = 'Failed to reorder images. Please try again.';
                        this.loadImages();
                    }
                },

                async handleDelete(image, index) {
                    if (! confirm('Are you sure you want to delete this image?')) return;

                    try {
                        const response = await this.$axios.delete(
                            `{{ route('admin.catalog.products.360_images.delete', ['productId' => '__PRODUCT__', 'imageId' => '__IMAGE__']) }}`
                                .replace('__PRODUCT__', this.productId)
                                .replace('__IMAGE__', image.id)
                        );

                        if (response.data.success) {
                            this.images.splice(index, 1);
                            this.images.forEach((img, idx) => { img.position = idx + 1; });
                            this.successMessage = 'Image deleted successfully';
                            setTimeout(() => { this.successMessage = null; }, 2000);
                        }
                    } catch (error) {
                        this.uploadError = error.response?.data?.message || 'Failed to delete image. Please try again.';
                    }
                },
            },
        });
    </script>

    <style>
        .draggable-ghost {
            opacity: 0.5;
            background: #f0f0f0;
        }
    </style>
@endPushOnce
