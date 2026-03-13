<?php

namespace Webkul\GoogleMerchant\Services;

use Google\Client;
use Google\Service\ShoppingContent;
use Google\Service\ShoppingContent\CustomAttribute;
use Google\Service\ShoppingContent\Price;
use Google\Service\ShoppingContent\Product;
use Google\Service\ShoppingContent\ProductDimension;
use Google\Service\ShoppingContent\ProductWeight;
use Webkul\Product\Contracts\Product as ProductModel;
use Webkul\Product\Models\ProductFlat;
use Webkul\Product\Repositories\ProductFlatRepository;
use Webkul\Product\Repositories\ProductRepository;

class MerchantService
{
    // -------------------------------------------------------------------------
    // Configuration constants
    // -------------------------------------------------------------------------

    private const CONFIG_MERCHANT_ID     = 'googlemerchant.settings.api_credentials.merchant_id';
    private const CONFIG_SERVICE_ACCOUNT = 'googlemerchant.settings.api_credentials.service_account_json';

    private const CONTENT_LANGUAGE = 'en';
    private const TARGET_COUNTRY   = 'IN';
    private const CURRENCY          = 'INR';
    private const CHANNEL           = 'online';
    private const CONDITION         = 'new';
    private const DIMENSION_UNIT    = 'cm';
    private const WEIGHT_UNIT       = 'g';

    // -------------------------------------------------------------------------
    // Lazy-initialised state
    // -------------------------------------------------------------------------

    private ?ShoppingContent $shoppingService = null;
    private ?string $merchantId = null;

    // -------------------------------------------------------------------------
    // Constructor
    // -------------------------------------------------------------------------

    public function __construct(
        protected ProductFlatRepository $productFlatRepository,
        protected ProductRepository $productRepository
    ) {}

    // -------------------------------------------------------------------------
    // Public API
    // -------------------------------------------------------------------------

    /**
     * Sync all active, individually-visible products to Google Merchant Center.
     *
     * @return array{synced: int, errors: array<int, array{product_id: int, error: string}>}
     */
    public function syncProducts(): array
    {
        $productFlats = $this->fetchActiveProductFlats();

        if ($productFlats->isEmpty()) {
            return ['synced' => 0, 'errors' => []];
        }

        $productModels = $this->fetchProductModels($productFlats->pluck('product_id'));

        $service    = $this->getShoppingService();
        $merchantId = $this->getMerchantId();
        $synced     = 0;
        $errors     = [];

        foreach ($productFlats as $productFlat) {
            try {
                $productModel = $productModels->get($productFlat->product_id)
                    ?? throw new \RuntimeException("Product model not found for ID {$productFlat->product_id}");

                $googleProduct = $this->buildGoogleProduct($productFlat, $productModel);

                $service->products->insert($merchantId, $googleProduct);

                $synced++;
            } catch (\Throwable $e) {
                $errors[] = [
                    'product_id' => $productFlat->product_id,
                    'error'      => $e->getMessage(),
                ];
            }
        }

        return compact('synced', 'errors');
    }

    // -------------------------------------------------------------------------
    // Product data builder
    // -------------------------------------------------------------------------

    /**
     * Map a Bagisto ProductFlat + ProductModel to a Google Shopping Content Product.
     *
     * @param  ProductFlat   $productFlat
     * @param  ProductModel  $productModel
     * @return Product
     */
    protected function buildGoogleProduct(ProductFlat $productFlat, ProductModel $productModel): Product
    {
        $product = new Product();

        $this->applyBasicInfo($product, $productFlat);
        $this->applyImages($product, $productModel);
        $this->applyAvailability($product, $productModel);
        $this->applyPricing($product, $productFlat);
        $this->applyShippingDimensions($product, $productModel);
        $this->applyCustomAttributes($product, $productFlat, $productModel);

        $product->setCondition(self::CONDITION);
        $product->setContentLanguage(self::CONTENT_LANGUAGE);
        $product->setTargetCountry(self::TARGET_COUNTRY);
        $product->setChannel(self::CHANNEL);
        $product->setBrand(config('app.name', 'Default Brand'));
        $product->setAdult(false);

        return $product;
    }

    // -------------------------------------------------------------------------
    // Build sub-steps (each handles one concern)
    // -------------------------------------------------------------------------

    private function applyBasicInfo(Product $product, ProductFlat $productFlat): void
    {
        $product->setOfferId((string) $productFlat->product_id);
        $product->setTitle((string) $productFlat->name);
        $product->setDescription(strip_tags((string) $productFlat->description));
        $product->setLink(url($productFlat->url_key));
    }

    private function applyImages(Product $product, ProductModel $productModel): void
    {
        if ($baseImage = $productModel->base_image_url ?? null) {
            $product->setImageLink((string) $baseImage);
        }

        $additionalImages = $productModel->images
            ?->filter(fn($img) => !empty($img->url))
            ->pluck('url')
            ->values()
            ->all() ?? [];

        if (!empty($additionalImages)) {
            $product->setAdditionalImageLinks($additionalImages);
        }
    }

    private function applyAvailability(Product $product, ProductModel $productModel): void
    {
        $quantity = $productModel->getTypeInstance()->totalQuantity()
            ?? $productModel->inventories->sum('qty');

        $product->setAvailability($quantity > 0 ? 'in_stock' : 'out_of_stock');
    }

    private function applyPricing(Product $product, ProductFlat $productFlat): void
    {
        $product->setPrice($this->buildPrice((float) $productFlat->price));

        if (
            !empty($productFlat->special_price)
            && (float) $productFlat->special_price < (float) $productFlat->price
        ) {
            $product->setSalePrice($this->buildPrice((float) $productFlat->special_price));
        }
    }

    private function applyShippingDimensions(Product $product, ProductModel $productModel): void
    {
        $attrs = $productModel->toArray();

        $dimensionMap = [
            'length' => 'setProductLength',
            'width'  => 'setProductWidth',
            'height' => 'setProductHeight',
        ];

        foreach ($dimensionMap as $field => $setter) {
            if (!empty($attrs[$field])) {
                $dimension = new ProductDimension();
                $dimension->setValue((float) $attrs[$field]);
                $dimension->setUnit(self::DIMENSION_UNIT);
                $product->$setter($dimension);
            }
        }

        if (!empty($attrs['weight'])) {
            $weight = new ProductWeight();
            $weight->setValue((float) $attrs['weight']);
            $weight->setUnit(self::WEIGHT_UNIT);
            $product->setProductWeight($weight);
        }
    }

    private function applyCustomAttributes(Product $product, ProductFlat $productFlat, ProductModel $productModel): void
    {
        $rawAttributes = [
            'sku'                  => $productModel->sku,
            'slug'                 => $productFlat->url_key,
            'is_new'               => $productFlat->new ? 'Yes' : 'No',
            'featured'             => $productFlat->featured ? 'Yes' : 'No',
            'visible_individually' => $productFlat->visible_individually ? 'Yes' : 'No',
            'short_description'    => strip_tags((string) $productFlat->short_description),
            'best_selling'         => $productFlat->best_selling ? 'Yes' : 'No'
        ];

        $customAttributes = [];

        foreach ($rawAttributes as $name => $value) {
            if (!empty($value)) {
                $attr = new CustomAttribute();
                $attr->setName($name);
                $attr->setValue((string) $value);
                $customAttributes[] = $attr;
            }
        }

        if (!empty($customAttributes)) {
            $product->setCustomAttributes($customAttributes);
        }
    }

    // -------------------------------------------------------------------------
    // Data-fetching helpers
    // -------------------------------------------------------------------------

    /**
     * Fetch active, individually-visible products for the current channel/locale.
     */
    private function fetchActiveProductFlats()
    {
        return $this->productFlatRepository->scopeQuery(
            fn($query) => $query
                ->where('status', 1)
                // ->where('visible_individually', 1)
                ->where('channel', core()->getCurrentChannelCode())
                ->where('locale', app()->getLocale())
        )->all();
    }

    /**
     * Fetch full product models (with images & inventories) keyed by ID.
     *
     * @param  \Illuminate\Support\Collection  $productIds
     * @return \Illuminate\Support\Collection
     */
    private function fetchProductModels($productIds)
    {
        return $this->productRepository
            ->with(['images', 'inventories'])
            ->scopeQuery(fn($q) => $q->whereIn('id', $productIds->unique()))
            ->all()
            ->keyBy('id');
    }

    // -------------------------------------------------------------------------
    // Google API helpers
    // -------------------------------------------------------------------------

    /**
     * Build a Google Price object.
     */
    private function buildPrice(float $amount): Price
    {
        $price = new Price();
        $price->setValue(number_format($amount, 2, '.', ''));
        $price->setCurrency(self::CURRENCY);

        return $price;
    }

    /**
     * Return the lazily-initialised Google Shopping Content service.
     */
    protected function getShoppingService(): ShoppingContent
    {
        if ($this->shoppingService === null) {
            $credentials = json_decode(
                core()->getConfigData(self::CONFIG_SERVICE_ACCOUNT),
                true
            );

            $client = new Client();
            $client->setAuthConfig($credentials);
            $client->addScope(ShoppingContent::CONTENT);

            $this->shoppingService = new ShoppingContent($client);
        }

        return $this->shoppingService;
    }

    /**
     * Return the lazily-resolved Merchant Center ID.
     */
    protected function getMerchantId(): string
    {
        return $this->merchantId ??= (string) core()->getConfigData(self::CONFIG_MERCHANT_ID);
    }
}
