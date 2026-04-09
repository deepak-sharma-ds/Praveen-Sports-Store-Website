{{-- Engraving Input Partial --}}
{{-- Usage: @include('shop::products.view.partials.engraving-input') --}}
{{-- Expects to be inside an Alpine.js or Vue scope with engravingText reactive --}}

<div
    class="engraving-option mt-4"
    x-data="{ engravingText: '' }"
>
    <label class="block text-sm font-medium text-gray-700 mb-2">
        &#9999;&#65039; Engraving <span class="text-gray-400">(optional, max 30 characters)</span>
    </label>

    <div class="relative">
        <input
            type="text"
            maxlength="30"
            x-model="engravingText"
            @input.debounce.400ms="if (window._batConfiguratorInstance) window._batConfiguratorInstance.applyEngraving($event.target.value)"
            placeholder="Enter engraving text..."
            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm transition-all focus:border-[#902129] focus:outline-none focus:ring-2 focus:ring-[#902129]/20"
        />

        <span
            class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-medium transition-colors"
            :class="(engravingText?.length || 0) >= 25 ? 'text-orange-500' : 'text-gray-400'"
            x-text="(engravingText?.length || 0) + '/30'"
        ></span>
    </div>

    <p
        class="mt-2 text-sm text-gray-500 italic"
        x-show="engravingText?.length > 0"
        x-transition
    >
        Preview: <span class="font-medium text-[#902129]" x-text="engravingText"></span>
    </p>
</div>
