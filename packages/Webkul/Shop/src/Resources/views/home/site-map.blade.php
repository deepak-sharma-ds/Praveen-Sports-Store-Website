<!-- Page Layout -->
<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        Site Map
    </x-slot>
    <section class="relative">
        <img class="object-cover max-h-[536px]" src="http://127.0.0.1:8000/storage/tinymce/cA5X9MMKtgU12Ou2UrdezzSg8KMNLBqMAnVFa7mg.png" alt="" width="1920" height="542">
        <div class="bg-black bg-opacity-35 w-full px-4 lg:px-14 content-center text-center text-white absolute inset-0">
            <h1 class="font-secondary text-2xl md:text-[40px] xl:text-[52px] font-normal uppercase">Sitemap</h1>
        </div>
    </section>
    <div class="bg-[#EDEDED] w-full px-4 lg:px-14 py-10 md:py-14">
        <ul class="grid md:grid-cols-2 gap-x-6 gap-y-2">
            @foreach ($allUrls as $url)
                <li>
                    <a href="{{ $url }}" class="text-black hover:underline">
                        {{ $url }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</x-shop::layouts>
