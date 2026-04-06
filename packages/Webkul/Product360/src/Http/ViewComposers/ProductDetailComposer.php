<?php

namespace Webkul\Product360\Http\ViewComposers;

use Illuminate\View\View;
use Webkul\Product360\Services\Product360Service;
use Webkul\Product360\DataTransferObjects\Product360ViewerConfig;
use Webkul\Product\Repositories\ProductRepository;

/**
 * ProductDetailComposer
 * 
 * View composer for shop product detail page.
 * Queries and passes 360 images to the view for frontend rendering.
 */
class ProductDetailComposer
{
    /**
     * Create a new composer instance.
     *
     * @param  \Webkul\Product360\Services\Product360Service  $product360Service
     */
    public function __construct(
        protected Product360Service $product360Service
    ) {}

    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view): void
    {
        // Try multiple methods to extract product instance from view
        $product = $view->getData()['product'] ?? null;
        
        // Fallback 1: Check shared data with gatherData()
        if (! $product) {
            try {
                $allData = $view->gatherData();
                $product = $allData['product'] ?? null;
            } catch (\Exception $e) {
                // gatherData() failed, continue
            }
        }
        
        // Fallback 2: Check route binding (only if we have a valid route)
        // Wrap in try-catch to handle test environments where routes may not be properly set up
        if (! $product) {
            try {
                if (app()->runningInConsole() === false && request()->route()) {
                    $route = request()->route();
                    if ($route) {
                        $product = $route->parameter('product') ?? $route->parameter('slug');
                    }
                }
            } catch (\Throwable $e) {
                // Route parameter extraction failed, continue to next fallback
            }
        }
        
        // Fallback 3: Resolve from route parameter with ProductRepository
        if (! $product) {
            try {
                if (app()->runningInConsole() === false && request()->route()) {
                    $route = request()->route();
                    if ($route && $route->parameter('id')) {
                        $productRepository = app(ProductRepository::class);
                        $product = $productRepository->findOrFail($route->parameter('id'));
                    }
                }
            } catch (\Throwable $e) {
                // Product not found or route not available, continue
            }
        }
        
        // Ensure product exists and has an ID before querying 360° images
        if (! $product || ! $product->id) {
            return;
        }
        
        // Query 360 images for this product (with caching)
        $images = $this->product360Service->getImagesForViewer($product->id);
        
        // Only pass images to view if count >= 2 (minimum for 360 viewer)
        if (count($images) >= 2) {
            $view->with('product360Images', $images);
            
            // Pass viewer configuration
            $config = new Product360ViewerConfig();
            $view->with('product360Config', $config->toArray());
        }
    }
}
