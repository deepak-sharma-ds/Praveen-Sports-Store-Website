@if ($product->getTypeInstance()->isCustomizable())
    @php
        $options = $product->customizable_options()->with([
            'product',
            'customizable_option_prices',
        ])->get();

        $optionCombinationImages = $product->option_combination_images;

        if (is_string($optionCombinationImages)) {
            $optionCombinationImages = json_decode($optionCombinationImages, true);
        }

        $optionCombinationImages = is_array($optionCombinationImages)
            ? $optionCombinationImages
            : [];

        $defaultGalleryImages = product_image()->getGalleryImages($product);
    @endphp

    @if ($options->isNotEmpty())
        {!! view_render_event('bagisto.shop.products.view.customizable-options.before', ['product' => $product]) !!}

        <v-product-customizable-options
            :initial-price="{{ $product->getTypeInstance()->getMinimalPrice() }}"
        >
        </v-product-customizable-options>

        {!! view_render_event('bagisto.shop.products.view.customizable-options.after', ['product' => $product]) !!}

        @pushOnce('scripts')
            <script
                type="text/x-template"
                id="v-product-customizable-options-template"
            >
                <div class="mt-8 max-sm:mt-0">
                    <template v-for="(option, index) in options">
                        <v-product-customizable-option-item
                            :option="option"
                            :key="index"
                            @priceUpdated="priceUpdated"
                            @optionSelectionUpdated="handleOptionSelectionUpdated"
                        >
                        </v-product-customizable-option-item>
                    </template>

                    <div class="mb-2.5 mt-5 flex items-center justify-between">
                        <p class="text-sm">
                            @lang('shop::app.products.view.type.simple.customizable-options.total-amount')
                        </p>

                        <p class="text-lg font-medium max-sm:text-sm">
                            @{{ formattedTotalPrice }}
                        </p>
                    </div>
                </div>
            </script>

            <script
                type="text/x-template"
                id="v-product-customizable-option-item-template"
            >
                <div class="mt-8 border-b border-zinc-200 pb-4 max-sm:mt-4 max-sm:pb-0">
                    <x-shop::form.control-group>
                        <!-- Text Field -->
                        <template v-if="option.type == 'text'">
                            <x-shop::form.control-group.label
                                class="!mt-0 max-sm:!mb-2.5"
                                ::class="{ 'required': Boolean(option.is_required) }"
                            >
                                @{{ option.label }}

                                <span class="text-black">
                                    @{{ '+ ' + $shop.formatPrice(option.price) }}
                                </span>
                            </x-shop::form.control-group.label>

                            <x-shop::form.control-group.control
                                type="text"
                                ::name="'customizable_options[' + option.id + '][]'"
                                ::value="option.id"
                                v-model="selectedItems"
                                ::rules="{ 'required': Boolean(option.is_required), 'max': option.max_characters }"
                                ::label="option.label"
                            />
                        </template>

                        <!-- Textarea Field -->
                        <template v-else-if="option.type == 'textarea'">
                            <x-shop::form.control-group.label
                                class="!mt-0 max-sm:!mb-2.5"
                                ::class="{ 'required': Boolean(option.is_required) }"
                            >
                                @{{ option.label }}

                                <span class="text-black">
                                    @{{ '+ ' + $shop.formatPrice(option.price) }}
                                </span>
                            </x-shop::form.control-group.label>

                            <x-shop::form.control-group.control
                                type="textarea"
                                ::name="'customizable_options[' + option.id + '][]'"
                                ::value="option.id"
                                v-model="selectedItems"
                                ::rules="{ 'required': Boolean(option.is_required), 'max': option.max_characters }"
                                ::label="option.label"
                            />
                        </template>

                        <!-- Checkbox Options -->
                        <template v-else-if="option.type == 'checkbox'">
                            <x-shop::form.control-group.label
                                class="!mt-0 max-sm:!mb-2.5"
                                ::class="{ 'required': Boolean(option.is_required) }"
                            >
                                @{{ option.label }}
                            </x-shop::form.control-group.label>

                            <div class="grid gap-2">
                                <div
                                    class="flex select-none items-center gap-x-4 max-sm:gap-x-1.5"
                                    v-for="(item, index) in optionItems"
                                >
                                    <x-shop::form.control-group.control
                                        type="checkbox"
                                        ::name="'customizable_options[' + option.id + '][]'"
                                        ::value="item.id"
                                        ::for="'customizable_options[' + option.id + '][' + index + ']'"
                                        ::id="'customizable_options[' + option.id + '][' + index + ']'"
                                        v-model="selectedItems"
                                        ::rules="{'required': Boolean(option.is_required)}"
                                        ::label="option.label"
                                    />

                                    <label
                                        class="cursor-pointer text-zinc-500 max-sm:text-sm"
                                        :for="'customizable_options[' + option.id + '][' + index + ']'"
                                    >
                                        @{{ item.label }}

                                        <span class="text-black">
                                            @{{ '+ ' + $shop.formatPrice(item.price) }}
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </template>

                        <!-- Radio Options -->
                        <template v-else-if="option.type == 'radio'">
                            <x-shop::form.control-group.label
                                class="!mt-0 max-sm:!mb-2.5"
                                ::class="{ 'required': Boolean(option.is_required) }"
                            >
                                @{{ option.label }}
                            </x-shop::form.control-group.label>

                            <div class="grid gap-2 max-sm:gap-1">
                                <!-- "None" radio option for cases where the option is not required. -->
                                <div
                                    class="flex select-none gap-x-4"
                                    v-if="! Boolean(option.is_required)"
                                >
                                    <x-shop::form.control-group.control
                                        type="radio"
                                        ::name="'customizable_options[' + option.id + '][]'"
                                        value="0"
                                        ::for="'customizable_options[' + option.id + '][' + index + ']'"
                                        ::id="'customizable_options[' + option.id + '][' + index + ']'"
                                        v-model="selectedItems"
                                        ::rules="{'required': Boolean(option.is_required)}"
                                        ::label="option.label"
                                        ::checked="true"
                                    />

                                    <label
                                        class="cursor-pointer text-zinc-500 max-sm:text-sm"
                                        :for="'customizable_options[' + option.id + '][' + index + ']'"
                                    >
                                        @lang('shop::app.products.view.type.simple.customizable-options.none')
                                    </label>
                                </div>

                                <!-- Options -->
                                <div
                                    class="flex select-none items-center gap-x-4 max-sm:gap-x-1.5"
                                    v-for="(item, index) in optionItems"
                                >
                                    <x-shop::form.control-group.control
                                        type="radio"
                                        ::name="'customizable_options[' + option.id + '][]'"
                                        ::value="item.id"
                                        ::for="'customizable_options[' + option.id + '][' + index + ']'"
                                        ::id="'customizable_options[' + option.id + '][' + index + ']'"
                                        v-model="selectedItems"
                                        ::rules="{'required': Boolean(option.is_required)}"
                                        ::label="option.label"
                                    />

                                    <label
                                        class="cursor-pointer text-zinc-500 max-sm:text-sm"
                                        :for="'customizable_options[' + option.id + '][' + index + ']'"
                                    >
                                        @{{ item.label }}

                                        <span class="text-black">
                                            @{{ '+ ' + $shop.formatPrice(item.price) }}
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </template>

                        <!-- Select Options -->
                        <template v-else-if="option.type == 'select'">
                            <x-shop::form.control-group.label
                                class="!mt-0 max-sm:!mb-2.5"
                                ::class="{ 'required': Boolean(option.is_required) }"
                            >
                                @{{ option.label }}
                            </x-shop::form.control-group.label>

                            <div
                                class="mt-4 flex flex-wrap items-center gap-3"
                                role="radiogroup"
                                :aria-label="option.label"
                            >
                                <label
                                    v-if="! Boolean(option.is_required)"
                                    class="group relative flex h-fit min-w-fit cursor-pointer items-center justify-center rounded-full border border-gray-300 bg-white px-5 py-3 font-medium text-gray-900 transition-all hover:bg-gray-50 max-sm:px-3.5 max-sm:py-2"
                                    :class="{ 'border-transparent !bg-navyBlue text-white': isItemSelected('0') }"
                                >
                                    <input
                                        type="radio"
                                        class="peer sr-only"
                                        :name="'customizable_option_swatch_' + option.id"
                                        value="0"
                                        :checked="isItemSelected('0')"
                                        @change="selectSwatchOption('0')"
                                    />

                                    <span class="text-sm font-medium max-sm:text-xs">
                                        @lang('shop::app.products.view.type.simple.customizable-options.none')
                                    </span>
                                </label>

                                <template
                                    v-for="(item, index) in optionItems"
                                    :key="item.id"
                                >
                                    <label
                                        v-if="isColorBasedOption()"
                                        class="relative -m-0.5 flex cursor-pointer items-center justify-center rounded-full p-0.5 transition-all focus-within:outline-none"
                                        :class="{ 'ring-2 ring-gray-900 ring-offset-2': isItemSelected(item.id) }"
                                        :for="'customizable_option_swatch_' + option.id + '_' + index"
                                        :title="getSwatchAccessibleLabel(item)"
                                    >
                                        <input
                                            type="radio"
                                            class="peer sr-only"
                                            :id="'customizable_option_swatch_' + option.id + '_' + index"
                                            :name="'customizable_option_swatch_' + option.id"
                                            :value="item.id"
                                            :checked="isItemSelected(item.id)"
                                            :aria-label="getSwatchAccessibleLabel(item)"
                                            @change="selectSwatchOption(item.id)"
                                        />

                                        <span
                                            class="h-8 w-8 rounded-full border border-gray-200 shadow-sm max-sm:h-[25px] max-sm:w-[25px]"
                                            :style="getColorSwatchStyle(item)"
                                            role="presentation"
                                        ></span>
                                    </label>

                                    <label
                                        v-else
                                        class="group relative flex h-fit min-w-fit cursor-pointer items-center justify-center rounded-full border border-gray-300 bg-white px-5 py-3 font-medium text-gray-900 transition-all hover:bg-gray-50 max-sm:px-3.5 max-sm:py-2"
                                        :class="{ 'border-transparent !bg-navyBlue text-white': isItemSelected(item.id) }"
                                        :for="'customizable_option_swatch_' + option.id + '_' + index"
                                        :title="item.display_label"
                                    >
                                        <input
                                            type="radio"
                                            class="peer sr-only"
                                            :id="'customizable_option_swatch_' + option.id + '_' + index"
                                            :name="'customizable_option_swatch_' + option.id"
                                            :value="item.id"
                                            :checked="isItemSelected(item.id)"
                                            :aria-label="getSwatchAccessibleLabel(item)"
                                            @change="selectSwatchOption(item.id)"
                                        />

                                        <span class="text-sm font-medium max-sm:text-xs">
                                            @{{ item.display_label }}
                                        </span>

                                        <span
                                            v-if="hasOptionPrice(item)"
                                            class="ml-2 text-xs font-medium opacity-80 max-sm:ml-1"
                                        >
                                            + @{{ $shop.formatPrice(item.price) }}
                                        </span>
                                    </label>
                                </template>
                            </div>

                            <v-field
                                as="select"
                                class="sr-only"
                                :id="'customizable_option_select_' + option.id"
                                :name="'customizable_options[' + option.id + '][]'"
                                v-model="selectedItems"
                                :rules="{'required': Boolean(option.is_required)}"
                                :label="option.label"
                                :aria-label="option.label"
                            >
                                <option
                                    value=""
                                    disabled
                                    v-if="Boolean(option.is_required)"
                                >
                                    @lang('shop::app.products.view.type.configurable.select-options')
                                </option>

                                <option
                                    value="0"
                                    v-if="! Boolean(option.is_required)"
                                >
                                    @lang('shop::app.products.view.type.simple.customizable-options.none')
                                </option>

                                <option
                                    v-for="item in optionItems"
                                    :value="item.id"
                                >
                                    @{{ getSwatchOptionText(item) }}
                                </option>
                            </v-field>
                        </template>

                        <!-- Multiselect Options -->
                        <template v-else-if="option.type == 'multiselect'">
                            <x-shop::form.control-group.label
                                class="!mt-0 max-sm:!mb-2.5"
                                ::class="{ 'required': Boolean(option.is_required) }"
                            >
                                @{{ option.label }}
                            </x-shop::form.control-group.label>

                            <x-shop::form.control-group.control
                                type="multiselect"
                                ::name="'customizable_options[' + option.id + '][]'"
                                v-model="selectedItems"
                                ::rules="{'required': Boolean(option.is_required)}"
                                ::label="option.label"
                            >
                                <option
                                    v-for="item in optionItems"
                                    :value="item.id"
                                    :selected="value && value.includes(item.id)"
                                >
                                    @{{ item.label + ' + ' + $shop.formatPrice(item.price) }}
                                </option>
                            </x-shop::form.control-group.control>
                        </template>

                        <!-- Date Field -->
                        <template v-else-if="option.type == 'date'">
                            <x-shop::form.control-group.label
                                class="!mt-0 max-sm:!mb-2.5"
                                ::class="{ 'required': Boolean(option.is_required) }"
                            >
                                @{{ option.label }}

                                <span class="text-black">
                                    @{{ '+ ' + $shop.formatPrice(option.price) }}
                                </span>
                            </x-shop::form.control-group.label>

                            <x-shop::form.control-group.control
                                type="date"
                                ::name="'customizable_options[' + option.id + '][]'"
                                ::value="option.id"
                                v-model="selectedItems"
                                ::rules="{'required': Boolean(option.is_required)}"
                                ::label="option.label"
                            />
                        </template>

                        <!-- Datetime Field -->
                        <template v-else-if="option.type == 'datetime'">
                            <x-shop::form.control-group.label
                                class="!mt-0 max-sm:!mb-2.5"
                                ::class="{ 'required': Boolean(option.is_required) }"
                            >
                                @{{ option.label }}

                                <span class="text-black">
                                    @{{ '+ ' + $shop.formatPrice(option.price) }}
                                </span>
                            </x-shop::form.control-group.label>

                            <x-shop::form.control-group.control
                                type="datetime"
                                ::name="'customizable_options[' + option.id + '][]'"
                                ::value="option.id"
                                v-model="selectedItems"
                                ::rules="{'required': Boolean(option.is_required)}"
                                ::label="option.label"
                            />
                        </template>

                        <!-- Time Field -->
                        <template v-else-if="option.type == 'time'">
                            <x-shop::form.control-group.label
                                class="!mt-0 max-sm:!mb-2.5"
                                ::class="{ 'required': Boolean(option.is_required) }"
                            >
                                @{{ option.label }}

                                <span class="text-black">
                                    @{{ '+ ' + $shop.formatPrice(option.price) }}
                                </span>
                            </x-shop::form.control-group.label>

                            <v-field
                                type="time"
                                :name="'customizable_options[' + option.id + '][]'"
                                :value="option.id"
                                v-model="selectedItems"
                                :rules="{'required': Boolean(option.is_required)}"
                                :label="option.label"
                            />
                        </template>

                        <!-- File -->
                        <template v-else-if="option.type == 'file'">
                            <x-shop::form.control-group.label
                                class="!mt-0 max-sm:!mb-2.5"
                                ::class="{ 'required': Boolean(option.is_required) }"
                            >
                                @{{ option.label }}

                                <span class="text-black">
                                    @{{ '+ ' + $shop.formatPrice(option.price) }}
                                </span>
                            </x-shop::form.control-group.label>

                            <v-field
                                type="file"
                                :name="'customizable_options[' + option.id + '][]'"
                                :rules="{'required': Boolean(option.is_required), ...(option.supported_file_extensions && option.supported_file_extensions.length ? {'ext': option.supported_file_extensions.split(',').map(ext => ext.trim())} : {})}"
                                :label="option.label"
                                @change="handleFileChange"
                            >
                            </v-field>
                        </template>

                        <x-shop::form.control-group.error ::name="'customizable_options[' + option.id + '][]'" />
                    </x-shop::form.control-group>
                </div>
            </script>

            <script type="module">
                const combinationImageConfig = @json($optionCombinationImages);

                const defaultGalleryImages = @json($defaultGalleryImages);

                const appBaseUrl = @json(rtrim(url('/'), '/'));

                const productStorageBaseUrl = @json(rtrim(url('/'), '/') . '/storage/product/' . $product->id);

                const defaultSelectedOptions = (
                    combinationImageConfig
                    && Array.isArray(combinationImageConfig.image_attributes)
                        ? combinationImageConfig.image_attributes
                        : []
                ).reduce((selectedOptions, attributeCode) => {
                    selectedOptions[attributeCode] = null;

                    return selectedOptions;
                }, {});

                app.component('v-product-customizable-options', {
                    template: '#v-product-customizable-options-template',

                    props: {
                        initialPrice: {
                            type: Number,

                            required: true,
                        },
                    },

                    data: function() {
                        return {
                            options: @json($options),

                            prices: [],

                            combinationImageConfig,

                            defaultGalleryImages,

                            selectedOptions: {...defaultSelectedOptions},

                            galleryUpdateTimer: null,

                            lastAppliedGallerySignature: null,
                        }
                    },

                    mounted() {
                        this.options = this.options.map((option) => {
                            if (! this.canHaveMultiplePriceOptions(option.type)) {
                                return {
                                    id: option.id,
                                    label: option.label,
                                    type: option.type,
                                    is_required: option.is_required,
                                    max_characters: option.max_characters,
                                    supported_file_extensions: option.supported_file_extensions,
                                    price_id: option.customizable_option_prices[0].id,
                                    price: option.customizable_option_prices[0].price,
                                };
                            }

                            return {
                                id: option.id,
                                label: option.label,
                                type: option.type,
                                is_required: option.is_required,
                                max_characters: option.max_characters,
                                supported_file_extensions: option.supported_file_extensions,
                                price: 0,
                            };
                        });

                        this.prices = this.options.map((option) => {
                            return {
                                option_id: option.id,
                                price: 0,
                            };
                        });

                        this.scheduleGallerySync();
                    },

                    beforeUnmount() {
                        if (this.galleryUpdateTimer) {
                            clearTimeout(this.galleryUpdateTimer);
                        }
                    },

                    computed: {
                        formattedTotalPrice: function() {
                            let totalPrice = this.initialPrice;

                            for (let price of this.prices) {
                                totalPrice += parseFloat(price.price);
                            }

                            return this.$shop.formatPrice(totalPrice);
                        },

                        imageAttributeOrder() {
                            if (
                                ! this.combinationImageConfig
                                || ! Array.isArray(this.combinationImageConfig.image_attributes)
                            ) {
                                return [];
                            }

                            return this.combinationImageConfig.image_attributes;
                        },

                        imageCombinationMap() {
                            if (
                                ! this.combinationImageConfig
                                || typeof this.combinationImageConfig.map !== 'object'
                                || this.combinationImageConfig.map === null
                            ) {
                                return {};
                            }

                            return this.combinationImageConfig.map;
                        },

                        optionAttributeMap() {
                            return this.options.reduce((attributeMap, option) => {
                                const attributeCode = this.resolveImageAttributeCode(option);

                                if (attributeCode) {
                                    attributeMap[String(option.id)] = attributeCode;
                                }

                                return attributeMap;
                            }, {});
                        },

                        matchedImagePath() {
                            return this.findBestMatch();
                        },

                        resolvedGalleryImages() {
                            if (! this.matchedImagePath) {
                                return this.defaultGalleryImages;
                            }

                            const matchedImage = this.createGalleryImage(this.matchedImagePath);

                            const remainingImages = this.defaultGalleryImages.filter((image) => {
                                return (image.large_image_url || image.original_image_url) !== this.matchedImagePath;
                            });

                            return [matchedImage, ...remainingImages];
                        },

                        resolvedGallerySignature() {
                            return JSON.stringify(this.resolvedGalleryImages.map((image) => {
                                return image.large_image_url || image.original_image_url || image.video_url || '';
                            }));
                        },
                    },

                    watch: {
                        selectedOptions: {
                            deep: true,

                            handler() {
                                this.scheduleGallerySync();
                            },
                        },

                        resolvedGallerySignature: {
                            immediate: true,

                            handler() {
                                this.scheduleGallerySync();
                            },
                        },
                    },

                    methods: {
                        priceUpdated({ option, totalPrice }) {
                            let price = this.prices.find(price => price.option_id === option.id);

                            price.price = totalPrice;
                        },

                        canHaveMultiplePriceOptions(type) {
                            return ['checkbox', 'radio', 'select', 'multiselect'].includes(type);
                        },

                        handleOptionSelectionUpdated({ optionId, value }) {
                            const attributeCode = this.optionAttributeMap[String(optionId)];

                            if (
                                ! attributeCode
                                || ! Object.prototype.hasOwnProperty.call(this.selectedOptions, attributeCode)
                            ) {
                                return;
                            }

                            this.selectedOptions = {
                                ...this.selectedOptions,
                                [attributeCode]: value,
                            };
                        },

                        normalize(value) {
                            return String(value ?? '')
                                .trim()
                                .toLowerCase()
                                .replace(/#/g, '')
                                .replace(/\s+/g, '-');
                        },

                        normalizeAttributeIdentifier(value) {
                            return String(value ?? '')
                                .trim()
                                .toLowerCase()
                                .replace(/[#\s_-]+/g, '');
                        },

                        resolveImageAttributeCode(option) {
                            const optionIdentifier = this.normalizeAttributeIdentifier(
                                option && option.label ? option.label : ''
                            );

                            return this.imageAttributeOrder.find((attributeCode) => {
                                return this.normalizeAttributeIdentifier(attributeCode) === optionIdentifier;
                            }) || null;
                        },

                        getOrderedSelectedValues() {
                            const orderedValues = [];

                            for (const attributeCode of this.imageAttributeOrder) {
                                const selectedValue = this.selectedOptions[attributeCode];

                                if (
                                    selectedValue === null
                                    || selectedValue === undefined
                                    || selectedValue === ''
                                    || String(selectedValue) === '0'
                                ) {
                                    break;
                                }

                                const normalizedValue = this.normalize(selectedValue);

                                if (! normalizedValue) {
                                    break;
                                }

                                orderedValues.push(normalizedValue);
                            }

                            return orderedValues;
                        },

                        generateKey(values = this.getOrderedSelectedValues()) {
                            return values.filter(Boolean).join('_');
                        },

                        findBestMatch() {
                            const selectedValues = [...this.getOrderedSelectedValues()];

                            if (! selectedValues.length) {
                                return null;
                            }

                            while (selectedValues.length) {
                                const candidateKey = this.generateKey(selectedValues);

                                if (this.imageCombinationMap[candidateKey]) {
                                    return this.resolveImageUrl(this.imageCombinationMap[candidateKey]);
                                }

                                selectedValues.pop();
                            }

                            return null;
                        },

                        resolveImageUrl(path) {
                            const imagePath = String(path ?? '').trim();

                            if (! imagePath) {
                                return null;
                            }

                            if (
                                /^(?:https?:)?\/\//.test(imagePath)
                                || imagePath.startsWith('data:')
                                || imagePath.startsWith('blob:')
                            ) {
                                return imagePath;
                            }

                            if (
                                imagePath.startsWith('/storage/')
                                || imagePath.startsWith('/cache/')
                                || imagePath.startsWith('/product/')
                            ) {
                                return `${appBaseUrl}${imagePath}`;
                            }

                            if (
                                imagePath.startsWith('storage/')
                                || imagePath.startsWith('cache/')
                                || imagePath.startsWith('product/')
                            ) {
                                return `${appBaseUrl}/${imagePath.replace(/^\/+/, '')}`;
                            }

                            return `${productStorageBaseUrl}/${imagePath.replace(/^\/+/, '')}`;
                        },

                        createGalleryImage(path) {
                            return {
                                type: 'image',
                                small_image_url: path,
                                medium_image_url: path,
                                large_image_url: path,
                                original_image_url: path,
                            };
                        },

                        scheduleGallerySync() {
                            if (this.galleryUpdateTimer) {
                                clearTimeout(this.galleryUpdateTimer);
                            }

                            this.galleryUpdateTimer = window.setTimeout(() => {
                                this.updateGalleryImages();
                            }, 40);
                        },

                        updateGalleryImages() {
                            const gallery = this.getGalleryComponent();
                            const images = this.resolvedGalleryImages.map((image) => ({...image}));
                            const nextBaseImagePath = images[0] ? images[0].large_image_url : null;
                            const isAlreadySynced = (
                                this.lastAppliedGallerySignature === this.resolvedGallerySignature
                                && gallery
                                && gallery.activeIndex === 0
                                && (
                                    ! nextBaseImagePath
                                    || (
                                        gallery.baseFile.type === 'image'
                                        && gallery.baseFile.path === nextBaseImagePath
                                    )
                                )
                            );

                            if (
                                ! gallery
                                || isAlreadySynced
                            ) {
                                return;
                            }

                            this.lastAppliedGallerySignature = this.resolvedGallerySignature;

                            gallery.activeIndex = 0;
                            gallery.media.images = images;

                            if (! images.length) {
                                gallery.isMediaLoading = false;

                                return;
                            }

                            const hasBaseImageChanged = (
                                gallery.baseFile.type !== 'image'
                                || gallery.baseFile.path !== nextBaseImagePath
                            );

                            gallery.baseFile.type = 'image';
                            gallery.baseFile.path = nextBaseImagePath;
                            gallery.isMediaLoading = hasBaseImageChanged;
                        },

                        getGalleryComponent() {
                            if (
                                ! this.$parent
                                || ! this.$parent.$parent
                                || ! this.$parent.$parent.$refs
                            ) {
                                return null;
                            }

                            return this.$parent.$parent.$refs.gallery || null;
                        },
                    }
                });

                app.component('v-product-customizable-option-item', {
                    template: '#v-product-customizable-option-item-template',

                    emits: ['priceUpdated', 'optionSelectionUpdated'],

                    props: ['option'],

                    data: function() {
                        return {
                            optionItems: [],

                            selectedItems: this.canHaveMultiplePrices() ? [] : null,
                        };
                    },

                    mounted() {
                        if (! this.option.customizable_option_prices) {
                            return;
                        }

                        this.optionItems = this.option.customizable_option_prices.map(optionItem => {
                            const parsedLabel = this.parseSwatchLabel(optionItem.label);

                            return {
                                id: String(optionItem.id),
                                label: optionItem.label,
                                display_label: parsedLabel.displayLabel,
                                accessible_label: parsedLabel.accessibleLabel,
                                swatch_color: parsedLabel.hex,
                                price: optionItem.price,
                            };
                        });

                        this.initializeDefaultSelection();
                    },

                    watch: {
                        selectedItems: function (value) {
                            let totalPrice = 0;
                            let selectedItemValues = Array.isArray(value)
                                ? value.map(item => String(item))
                                : [value === null || value === undefined ? '' : String(value)];

                            for (let item of this.optionItems) {
                                switch (this.option.type) {
                                    case 'text':
                                    case 'textarea':
                                    case 'date':
                                    case 'datetime':
                                    case 'time':
                                        if (selectedItemValues[0].length > 0) {
                                            totalPrice += parseFloat(item.price);
                                        }

                                        break;

                                    case 'checkbox':
                                    case 'radio':
                                    case 'select':
                                    case 'multiselect':
                                        if (selectedItemValues.includes(String(item.id))) {
                                            totalPrice += parseFloat(item.price);
                                        }

                                        break;

                                    case 'file':
                                        if (value instanceof File) {
                                            totalPrice += parseFloat(item.price);
                                        }

                                        break;
                                }
                            }

                            this.$emit('priceUpdated', {
                                option: this.option,

                                totalPrice,
                            });

                            this.emitOptionSelectionUpdated(value);
                        },
                    },

                    methods: {
                        canHaveMultiplePrices() {
                            return ['checkbox', 'multiselect'].includes(this.option.type);
                        },

                        initializeDefaultSelection() {
                            if (this.option.type !== 'select' || ! this.optionItems.length) {
                                return;
                            }

                            this.selectedItems = this.optionItems[0].id;
                        },

                        isColorBasedOption() {
                            return /colou?r/i.test(this.option.label || '');
                        },

                        isHexColor(value) {
                            return /^#(?:[0-9a-fA-F]{3}){1,2}$/.test((value || '').trim());
                        },

                        parseSwatchLabel(label) {
                            const rawLabel = (label || '').trim();
                            const parts = rawLabel.split('|').map(part => part.trim()).filter(Boolean);
                            const hex = parts.find(part => this.isHexColor(part)) || (this.isHexColor(rawLabel) ? rawLabel : null);
                            const displayLabel = parts.find(part => ! this.isHexColor(part)) || rawLabel;

                            return {
                                displayLabel,
                                accessibleLabel: displayLabel || rawLabel || hex || this.option.label,
                                hex,
                            };
                        },

                        isItemSelected(itemId) {
                            return String(this.selectedItems) === String(itemId);
                        },

                        selectSwatchOption(itemId) {
                            this.selectedItems = String(itemId);
                        },

                        emitOptionSelectionUpdated(value) {
                            this.$emit('optionSelectionUpdated', {
                                optionId: this.option.id,
                                value: this.getSelectedImageAttributeValue(value),
                            });

                            this.sync3DConfigurator(value);
                        },

                        getSelectedImageAttributeValue(value) {
                            if (! ['select', 'radio'].includes(this.option.type)) {
                                return null;
                            }

                            const selectedItemId = Array.isArray(value)
                                ? null
                                : String(value ?? '');

                            if (! selectedItemId || selectedItemId === '0') {
                                return null;
                            }

                            const selectedItem = this.optionItems.find((item) => {
                                return String(item.id) === selectedItemId;
                            });

                            if (! selectedItem) {
                                return null;
                            }

                            return selectedItem.swatch_color || selectedItem.display_label || selectedItem.label;
                        },

                        getSwatchAccessibleLabel(item) {
                            return item.accessible_label || item.display_label || item.label;
                        },

                        getColorSwatchStyle(item) {
                            return {
                                backgroundColor: item.swatch_color || '#e5e7eb',
                            };
                        },

                        hasOptionPrice(item) {
                            return parseFloat(item.price) > 0;
                        },

                        getSwatchOptionText(item) {
                            const priceLabel = this.hasOptionPrice(item)
                                ? ` + ${this.$shop.formatPrice(item.price)}`
                                : '';

                            return `${item.display_label}${priceLabel}`;
                        },

                        handleFileChange($event) {
                            const selectedFiles = $event.target.files;

                            this.selectedItems = selectedFiles[0];
                        },

                        /**
                         * Bridge: push option changes to the Three.js 3D configurator.
                         */
                        sync3DConfigurator(value) {
                            const inst = window._batConfiguratorInstance;
                            if (!inst) return;

                            const label = (this.option.label || '').toLowerCase();

                            /* Resolve the display value for select/radio */
                            const resolveDisplayValue = (val) => {
                                const itemId = String(val ?? '');
                                if (!itemId || itemId === '0') return null;

                                const item = this.optionItems.find(i => String(i.id) === itemId);
                                return item || null;
                            };

                            /* Sticker Color */
                            if (/sticker[\s_-]*colou?r/i.test(label)) {
                                const item = resolveDisplayValue(value);
                                if (item && item.swatch_color) {
                                    inst.applyColor('Bat_Sticker', item.swatch_color);
                                }
                                return;
                            }

                            /* Grip Colour */
                            if (/grip[\s_-]*colou?r/i.test(label)) {
                                const item = resolveDisplayValue(value);
                                if (item && item.swatch_color) {
                                    inst.applyColor('Bat_Grip', item.swatch_color);
                                }
                                return;
                            }

                            /* Bat Profile */
                            if (/bat[\s_-]*profile/i.test(label)) {
                                const item = resolveDisplayValue(value);
                                if (item) {
                                    const val = (item.display_label || item.label || '').toLowerCase();
                                    inst.setBatProfile(val.includes('duckbill') ? 'duckbill' : 'full');
                                }
                                return;
                            }

                            /* Toe Shape */
                            if (/toe[\s_-]*shape/i.test(label)) {
                                const item = resolveDisplayValue(value);
                                if (item) {
                                    const val = (item.display_label || item.label || '').toLowerCase();
                                    inst.setToeShape(val.includes('round') ? 'round' : 'flat');
                                }
                                return;
                            }

                            /* Engraving (text/textarea) */
                            if (/engrav/i.test(label)) {
                                const text = typeof value === 'string' ? value : '';
                                if (this._engravingDebounce) clearTimeout(this._engravingDebounce);
                                this._engravingDebounce = setTimeout(() => {
                                    inst.applyEngraving(text);
                                }, 400);
                                return;
                            }
                        },
                    },
                });
            </script>
        @endPushOnce
    @endif
@endif
