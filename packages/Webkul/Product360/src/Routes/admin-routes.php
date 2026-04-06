<?php

use Illuminate\Support\Facades\Route;
use Webkul\Product360\Http\Controllers\Admin\Product360Controller;


Route::group([
    'prefix'     => 'admin/catalog/products/{productId}/360-images',
    'middleware' => ['web', 'admin'],
], function () {
    Route::controller(Product360Controller::class)->group(function () {
        /**
         * Get all 360 images for a product.
         * GET /admin/catalog/products/{productId}/360-images
         */
        Route::get('', 'index')->name('admin.catalog.products.360_images.index');

        /**
         * Upload multiple 360 images for a product.
         * POST /admin/catalog/products/{productId}/360-images/upload
         */
        Route::post('upload', 'upload')->name('admin.catalog.products.360_images.upload');

        /**
         * Reorder 360 images for a product.
         * PUT /admin/catalog/products/{productId}/360-images/reorder
         */
        Route::put('reorder', 'reorder')->name('admin.catalog.products.360_images.reorder');

        /**
         * Delete a 360 image.
         * DELETE /admin/catalog/products/{productId}/360-images/{imageId}
         */
        Route::delete('{imageId}', 'delete')->name('admin.catalog.products.360_images.delete');
    });
});
