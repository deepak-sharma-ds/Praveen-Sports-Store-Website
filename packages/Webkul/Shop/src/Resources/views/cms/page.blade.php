<!-- SEO Meta Content -->
@push('meta')
    <meta name="title" content="{{ $page->meta_title }}" />

    <meta name="description" content="{{ $page->meta_description }}" />

    <meta name="keywords" content="{{ $page->meta_keywords }}" />
@endPush

<!-- Page Layout -->
<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        {{ $page->meta_title }}
    </x-slot>
    <section class="relative">
        <img class="object-cover max-h-[536px]" src="/storage/tinymce/cA5X9MMKtgU12Ou2UrdezzSg8KMNLBqMAnVFa7mg.png" alt="" width="1920" height="542">
        <div class="bg-black bg-opacity-35 w-full px-4 lg:px-14 content-center text-center text-white absolute inset-0">
            <h1 class="font-secondary text-2xl md:text-[40px] xl:text-[52px] font-normal uppercase">{!! $page->page_title !!}</h1>
        </div>
    </section>
    <!-- Page Content -->
    <div class="bg-[#EDEDED] w-full px-4 lg:px-14 py-10 md:py-14 space-y-4">
        {!! $page->html_content !!}
    </div>




</x-shop::layouts>
