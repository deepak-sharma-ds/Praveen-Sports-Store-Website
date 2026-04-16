<x-admin::layouts>

    <x-slot:title>
        @lang('brochure::app.admin.edit.title')
    </x-slot>

    <div class="flex items-center justify-between">
        <p class="text-xl font-bold text-gray-800 dark:text-white">
            @lang('brochure::app.admin.edit.title')
        </p>

        <div class="flex items-center gap-x-2.5">
            {{-- Preview link --}}
            <a
                href="{{ route('shop.brochure.view', $brochure->slug) }}"
                target="_blank"
                class="transparent-button"
            >
                @lang('brochure::app.admin.edit.preview-btn')
            </a>

            <a
                href="{{ route('admin.brochure.index') }}"
                class="transparent-button"
            >
                @lang('brochure::app.admin.edit.back-btn')
            </a>
        </div>
    </div>

    @if (session('error'))
        <div class="mt-3 rounded border border-red-200 bg-red-50 p-4 text-sm text-red-600 dark:border-red-700 dark:bg-red-900/20 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif

    <form
        action="{{ route('admin.brochure.update', $brochure->id) }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf
        @method('PUT')

        <div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">

            {{-- Left Column: Main Details --}}
            <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">

                {{-- Basic Information Card --}}
                <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                    <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                        @lang('brochure::app.admin.edit.general')
                    </p>

                    {{-- Title --}}
                    <div class="mb-4">
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('brochure::app.admin.fields.title')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                name="title"
                                :value="old('title', $brochure->title)"
                                :placeholder="trans('brochure::app.admin.fields.title-placeholder')"
                            />

                            @error('title')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </x-admin::form.control-group>
                    </div>

                    {{-- Slug (read-only, for info) --}}
                    <div class="mb-4">
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('brochure::app.admin.fields.slug')
                            </x-admin::form.control-group.label>

                            <div class="flex items-center rounded border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-500 dark:border-gray-800 dark:bg-gray-800 dark:text-gray-400">
                                {{ $brochure->slug }}
                                <span class="ml-2 text-xs text-gray-400 dark:text-gray-500">(auto-updated on title change)</span>
                            </div>
                        </x-admin::form.control-group>
                    </div>

                    {{-- Type --}}
                    <div class="mb-4">
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('brochure::app.admin.fields.type')
                            </x-admin::form.control-group.label>

                            <select
                                name="type"
                                id="brochure-type"
                                class="w-full rounded border border-gray-200 px-3 py-2.5 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                            >
                                <option value="pdf" {{ old('type', $brochure->type) === 'pdf' ? 'selected' : '' }}>
                                    @lang('brochure::app.admin.fields.type-pdf')
                                </option>
                                <option value="images" {{ old('type', $brochure->type) === 'images' ? 'selected' : '' }}>
                                    @lang('brochure::app.admin.fields.type-images')
                                </option>
                            </select>

                            @error('type')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </x-admin::form.control-group>
                    </div>

                    {{-- Current PDF info --}}
                    @if ($brochure->pdf_path)
                        <div class="mb-2 rounded border border-blue-100 bg-blue-50 px-3 py-2 text-xs text-blue-700 dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-300" id="current-pdf-info">
                            <span class="font-semibold">@lang('brochure::app.admin.edit.current-pdf'):</span>
                            <a href="{{ $brochure->pdf_url }}" target="_blank" class="ml-1 underline">
                                {{ basename($brochure->pdf_path) }}
                            </a>
                        </div>
                    @endif

                    {{-- PDF Upload (shown when type=pdf) --}}
                    <div class="mb-4" id="pdf-upload-section">
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('brochure::app.admin.edit.replace-pdf')
                            </x-admin::form.control-group.label>

                            <div class="flex flex-col gap-1">
                                <input
                                    type="file"
                                    name="pdf_file"
                                    accept=".pdf"
                                    class="w-full rounded border border-gray-200 px-3 py-2 text-sm text-gray-800 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                                />
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    @lang('brochure::app.admin.fields.pdf-replace-hint')
                                </p>
                            </div>

                            @error('pdf_file')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </x-admin::form.control-group>
                    </div>

                    {{-- Current page count for image mode --}}
                    @php $existingPages = $brochure->page_images; @endphp
                    @if (!empty($existingPages))
                        <div class="mb-2 rounded border border-blue-100 bg-blue-50 px-3 py-2 text-xs text-blue-700 dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-300" id="current-images-info">
                            <span class="font-semibold">@lang('brochure::app.admin.edit.current-pages'):</span>
                            {{ count($existingPages) }} @lang('brochure::app.admin.edit.pages')
                        </div>
                    @endif

                    {{-- Page Images Upload (shown when type=images) --}}
                    <div class="mb-4 hidden" id="images-upload-section">
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('brochure::app.admin.edit.replace-images')
                            </x-admin::form.control-group.label>

                            <div class="flex flex-col gap-1">
                                <input
                                    type="file"
                                    name="page_images[]"
                                    accept="image/*"
                                    multiple
                                    class="w-full rounded border border-gray-200 px-3 py-2 text-sm text-gray-800 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                                />
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    @lang('brochure::app.admin.fields.images-replace-hint')
                                </p>
                            </div>

                            @error('page_images')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </x-admin::form.control-group>
                    </div>
                </div>

                {{-- SEO Card --}}
                <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                    <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                        @lang('brochure::app.admin.edit.seo')
                    </p>

                    <div class="mb-4">
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('brochure::app.admin.fields.meta-title')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                name="meta_title"
                                :value="old('meta_title', $brochure->meta_title)"
                            />

                            @error('meta_title')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </x-admin::form.control-group>
                    </div>

                    <div class="mb-4">
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('brochure::app.admin.fields.meta-description')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="textarea"
                                name="meta_description"
                                rows="3"
                            >{{ old('meta_description', $brochure->meta_description) }}</x-admin::form.control-group.control>

                            @error('meta_description')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </x-admin::form.control-group>
                    </div>
                </div>

            </div>

            {{-- Right Column: Settings --}}
            <div class="flex w-[360px] max-w-full flex-col gap-2 max-sm:w-full">

                {{-- Status & Order Card --}}
                <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                    <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                        @lang('brochure::app.admin.edit.settings')
                    </p>

                    <div class="mb-4">
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('brochure::app.admin.fields.status')
                            </x-admin::form.control-group.label>

                            <select
                                name="status"
                                class="w-full rounded border border-gray-200 px-3 py-2.5 text-sm font-normal text-gray-800 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                            >
                                <option value="1" {{ old('status', $brochure->status ? '1' : '0') === '1' ? 'selected' : '' }}>
                                    @lang('brochure::app.admin.fields.status-active')
                                </option>
                                <option value="0" {{ old('status', $brochure->status ? '1' : '0') === '0' ? 'selected' : '' }}>
                                    @lang('brochure::app.admin.fields.status-inactive')
                                </option>
                            </select>

                            @error('status')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </x-admin::form.control-group>
                    </div>

                    <div class="mb-4">
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('brochure::app.admin.fields.sort-order')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="number"
                                name="sort_order"
                                :value="old('sort_order', $brochure->sort_order)"
                                min="0"
                            />

                            @error('sort_order')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </x-admin::form.control-group>
                    </div>
                </div>

                {{-- Cover Image Card --}}
                <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                    <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                        @lang('brochure::app.admin.edit.cover-image')
                    </p>

                    @if ($brochure->cover_image_url)
                        <div class="mb-3">
                            <img
                                src="{{ $brochure->cover_image_url }}"
                                alt="{{ $brochure->title }}"
                                class="h-40 w-auto rounded border border-gray-200 object-cover dark:border-gray-700"
                            />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                @lang('brochure::app.admin.edit.current-cover')
                            </p>
                        </div>
                    @endif

                    <div class="mb-2">
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('brochure::app.admin.fields.cover-image-replace')
                            </x-admin::form.control-group.label>

                            <div class="flex flex-col gap-1">
                                <input
                                    type="file"
                                    name="cover_image"
                                    accept="image/*"
                                    class="w-full rounded border border-gray-200 px-3 py-2 text-sm text-gray-800 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                                />
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    @lang('brochure::app.admin.fields.cover-image-replace-hint')
                                </p>
                            </div>

                            @error('cover_image')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </x-admin::form.control-group>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex justify-end gap-2">
                    <a href="{{ route('admin.brochure.index') }}" class="transparent-button">
                        @lang('brochure::app.admin.edit.cancel-btn')
                    </a>

                    <button type="submit" class="primary-button">
                        @lang('brochure::app.admin.edit.save-btn')
                    </button>
                </div>

            </div>
        </div>

    </form>

    <script>
        (function () {
            const typeSelect = document.getElementById('brochure-type');
            const pdfSection = document.getElementById('pdf-upload-section');
            const imgSection = document.getElementById('images-upload-section');
            const currentPdfInfo = document.getElementById('current-pdf-info');
            const currentImagesInfo = document.getElementById('current-images-info');

            function toggleSections() {
                if (typeSelect.value === 'pdf') {
                    pdfSection.classList.remove('hidden');
                    imgSection.classList.add('hidden');
                    if (currentPdfInfo) currentPdfInfo.classList.remove('hidden');
                    if (currentImagesInfo) currentImagesInfo.classList.add('hidden');
                } else {
                    pdfSection.classList.add('hidden');
                    imgSection.classList.remove('hidden');
                    if (currentPdfInfo) currentPdfInfo.classList.add('hidden');
                    if (currentImagesInfo) currentImagesInfo.classList.remove('hidden');
                }
            }

            typeSelect.addEventListener('change', toggleSections);
            toggleSections();
        })();
    </script>

</x-admin::layouts>
