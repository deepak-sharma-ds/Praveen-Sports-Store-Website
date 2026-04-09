<x-admin::layouts>

    <x-slot:title>
        @lang('brochure::app.admin.index.title')
    </x-slot>

    <div class="flex items-center justify-between">
        <p class="text-xl font-bold text-gray-800 dark:text-white">
            @lang('brochure::app.admin.index.title')
        </p>

        <div class="flex items-center gap-x-2.5">
            @if (bouncer()->hasPermission('brochure.create'))
                <a
                    href="{{ route('admin.brochure.create') }}"
                    class="primary-button"
                >
                    @lang('brochure::app.admin.index.create-btn')
                </a>
            @endif
        </div>
    </div>

    {!! view_render_event('bagisto.admin.brochure.list.before') !!}

    <x-admin::datagrid :src="route('admin.brochure.index')" />

    {!! view_render_event('bagisto.admin.brochure.list.after') !!}

</x-admin::layouts>
