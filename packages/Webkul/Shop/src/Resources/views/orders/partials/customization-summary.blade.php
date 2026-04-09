{{-- Customization Summary Partial --}}
{{-- Usage: @include('shop::orders.partials.customization-summary', ['item' => $item]) --}}

@if (! empty($item->additional['customization']))
    @php
        $customization = $item->additional['customization'];
        $imagePath = $customization['_image_path'] ?? null;
        $displayItems = collect($customization)->except('_image_path')->filter(fn($v) => $v !== null && $v !== '');
    @endphp

    <div class="mt-3 rounded-lg border border-gray-200 bg-gray-50 p-4">
        <p class="mb-3 text-sm font-semibold text-gray-700">Customization Details</p>

        {{-- Thumbnail from combination image --}}
        @if ($imagePath)
            <div class="mb-3">
                <img
                    src="{{ asset($imagePath) }}"
                    alt="Customized product preview"
                    class="h-20 w-20 rounded-lg border border-gray-200 object-contain bg-white"
                    loading="lazy"
                />
            </div>
        @endif

        {{-- Key-value grid --}}
        @if ($displayItems->isNotEmpty())
            <div class="grid grid-cols-2 gap-x-6 gap-y-2">
                @foreach ($displayItems as $key => $value)
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                            {{ str_replace('_', ' ', $key) }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        @if (preg_match('/^#[0-9a-fA-F]{3,8}$/', $value))
                            {{-- Color swatch --}}
                            <span
                                class="inline-block h-4 w-4 rounded-full border border-gray-300"
                                style="background-color: {{ $value }}"
                            ></span>
                            <span class="text-sm text-gray-700">{{ $value }}</span>
                        @else
                            <span class="text-sm text-gray-700">{{ $value }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endif
