<x-admin::layouts>

    <x-slot:title>
        @lang('brochure::app.admin.create.title')
    </x-slot>

    <div class="flex items-center justify-between">
        <p class="text-xl font-bold text-gray-800 dark:text-white">
            @lang('brochure::app.admin.create.title')
        </p>

        <div class="flex items-center gap-x-2.5">
            <a
                href="{{ route('admin.brochure.index') }}"
                class="transparent-button"
            >
                @lang('brochure::app.admin.create.back-btn')
            </a>
        </div>
    </div>

    @if (session('error'))
        <div class="mt-3 rounded border border-red-200 bg-red-50 p-4 text-sm text-red-600 dark:border-red-700 dark:bg-red-900/20 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif

    <form
        action="{{ route('admin.brochure.store') }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf

        <div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">

            {{-- Left Column: Main Details --}}
            <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">

                {{-- Basic Information Card --}}
                <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                    <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                        @lang('brochure::app.admin.create.general')
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
                                id="brochure-title"
                                :value="old('title')"
                                :placeholder="trans('brochure::app.admin.fields.title-placeholder')"
                                rules="required"
                            />

                            <x-admin::form.control-group.error field-name="title" />

                            @error('title')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
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
                                <option value="pdf" {{ old('type', 'pdf') === 'pdf' ? 'selected' : '' }}>
                                    @lang('brochure::app.admin.fields.type-pdf')
                                </option>
                                <option value="images" {{ old('type') === 'images' ? 'selected' : '' }}>
                                    @lang('brochure::app.admin.fields.type-images')
                                </option>
                            </select>

                            @error('type')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </x-admin::form.control-group>
                    </div>

                    {{-- PDF Upload (shown when type=pdf) --}}
                    <div class="mb-4" id="pdf-upload-section">
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('brochure::app.admin.fields.pdf-file')
                            </x-admin::form.control-group.label>

                            <div class="flex flex-col gap-1">
                                <input
                                    type="file"
                                    name="pdf_file"
                                    id="pdf_file"
                                    accept=".pdf"
                                    class="w-full rounded border border-gray-200 px-3 py-2 text-sm text-gray-800 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                                />
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    @lang('brochure::app.admin.fields.pdf-hint')
                                </p>
                            </div>

                            @error('pdf_file')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </x-admin::form.control-group>
                    </div>

                    {{-- Page Images Upload (shown when type=images) --}}
                    <div class="mb-4 hidden" id="images-upload-section">
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('brochure::app.admin.fields.page-images')
                            </x-admin::form.control-group.label>

                            <div class="flex flex-col gap-1">
                                <input
                                    type="file"
                                    name="page_images[]"
                                    id="page_images"
                                    accept="image/*"
                                    multiple
                                    class="w-full rounded border border-gray-200 px-3 py-2 text-sm text-gray-800 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                                />
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    @lang('brochure::app.admin.fields.images-hint')
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
                        @lang('brochure::app.admin.create.seo')
                    </p>

                    <div class="mb-4">
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('brochure::app.admin.fields.meta-title')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                name="meta_title"
                                :value="old('meta_title')"
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
                            >{{ old('meta_description') }}</x-admin::form.control-group.control>

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
                        @lang('brochure::app.admin.create.settings')
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
                                <option value="1" {{ old('status', '1') === '1' ? 'selected' : '' }}>
                                    @lang('brochure::app.admin.fields.status-active')
                                </option>
                                <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>
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
                                :value="old('sort_order', 0)"
                                min="0"
                            />

                            @error('sort_order')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </x-admin::form.control-group>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex justify-end gap-2">
                    <a href="{{ route('admin.brochure.index') }}" class="transparent-button">
                        @lang('brochure::app.admin.create.cancel-btn')
                    </a>

                    <button
                        type="submit"
                        class="primary-button"
                    >
                        @lang('brochure::app.admin.create.save-btn')
                    </button>
                </div>

            </div>
        </div>

    </form>

    {{-- JS: toggle PDF vs Images upload section based on type selection --}}
    <script>
        (function () {
            const typeSelect = document.getElementById('brochure-type');
            const pdfSection = document.getElementById('pdf-upload-section');
            const imgSection = document.getElementById('images-upload-section');

            function toggleSections() {
                if (typeSelect.value === 'pdf') {
                    pdfSection.classList.remove('hidden');
                    imgSection.classList.add('hidden');
                } else {
                    pdfSection.classList.add('hidden');
                    imgSection.classList.remove('hidden');
                }
            }

            typeSelect.addEventListener('change', toggleSections);
            toggleSections();
        })();
    </script>

</x-admin::layouts>
