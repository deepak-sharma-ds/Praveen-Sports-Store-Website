<?php

namespace Webkul\Product360\Providers;

use Illuminate\Support\ServiceProvider;
use Webkul\Product360\Console\Commands\CleanupOrphanedFiles;

class Product360ServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/product360.php',
            'product360'
        );
        
        $this->app->bind(
            \Webkul\Product360\Contracts\Product360Image::class,
            \Webkul\Product360\Models\Product360Image::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
        
        $this->loadRoutesFrom(__DIR__.'/../Routes/admin-routes.php');
        
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'product360');
        
        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'product360');
        
        // Register console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                CleanupOrphanedFiles::class,
            ]);
            
            // Publish assets
            $this->publishes([
                __DIR__.'/../Resources/assets/js' => public_path('vendor/product360/js'),
            ], 'product360-js');
            
            $this->publishes([
                __DIR__.'/../Resources/assets/css' => public_path('vendor/product360/css'),
            ], 'product360-css');
            
            // Publish all assets at once
            $this->publishes([
                __DIR__.'/../Resources/assets/js' => public_path('vendor/product360/js'),
                __DIR__.'/../Resources/assets/css' => public_path('vendor/product360/css'),
            ], 'product360-assets');
        }
        
        // Add relationship to Product model dynamically
        \Webkul\Product\Models\Product::resolveRelationUsing('product360Images', function ($productModel) {
            return $productModel->hasMany(\Webkul\Product360\Models\Product360Image::class, 'product_id');
        });
        
        // The 360° admin panel is registered as admin::catalog.products.edit.product-360-images
        // and @included directly in edit.blade.php — same pattern as images, videos, links panels.

        // Register view composers
        $this->registerViewComposers();
    }
    
    /**
     * Register view composers for passing 360 image data to views.
     *
     * @return void
     */
    protected function registerViewComposers(): void
    {
        // Shop product gallery - register view composer to pass 360 image data
        // Defer registration to ensure routes are loaded first
        $this->app->booted(function () {
            \View::composer(
                'shop::products.view.gallery',
                \Webkul\Product360\Http\ViewComposers\ProductDetailComposer::class
            );
        });
        
        // Admin product edit page composer
        // Defer registration to avoid conflicts with route loading
        $this->app->booted(function () {
            \View::composer(
                'product360::admin.catalog.products.edit.360-view',
                \Webkul\Product360\Http\ViewComposers\AdminProductEditComposer::class
            );
        });
    }
}
