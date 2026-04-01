<?php

namespace Webkul\Shop\Http\Helpers;

use Webkul\Product\Models\Product;

class CustomizationImageResolver
{
    /**
     * Resolve the best-matching combination image path for the given product and selected options.
     *
     * @param  \Webkul\Product\Models\Product  $product
     * @param  array  $selectedOptions  Associative array keyed by attribute code (e.g. ['sticker_color' => '#7AFF66', ...])
     * @return string|null  The relative .webp image path, or null if no match
     */
    public function resolve($product, array $selectedOptions): ?string
    {
        $raw = $product->option_combination_images;

        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }

        if (! is_array($raw) || empty($raw['image_attributes']) || empty($raw['map'])) {
            return null;
        }

        $imageAttributes = $raw['image_attributes'];
        $map = $raw['map'];

        // Build key segments in the order defined by image_attributes
        $segments = [];

        foreach ($imageAttributes as $attributeCode) {
            $value = $selectedOptions[$attributeCode] ?? null;

            if ($value === null || $value === '') {
                break;
            }

            $segments[] = $this->normalize($value);
        }

        // Try longest match first, then progressively shorter
        while (count($segments) > 0) {
            $key = implode('_', $segments);

            if (isset($map[$key])) {
                return $map[$key];
            }

            array_pop($segments);
        }

        return null;
    }

    /**
     * Normalize a value for key matching: strip #, lowercase, replace spaces with _.
     */
    protected function normalize(string $value): string
    {
        $value = trim($value);
        $value = strtolower($value);
        $value = str_replace('#', '', $value);
        $value = preg_replace('/\s+/', '_', $value);

        return $value;
    }
}
