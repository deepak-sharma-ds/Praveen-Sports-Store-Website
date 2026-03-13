<?php

use Illuminate\Support\Facades\Route;
use Webkul\GoogleMerchant\Http\Controllers\Admin\GoogleMerchantController;

Route::group(['prefix' => 'admin'], function () {
    Route::controller(GoogleMerchantController::class)->group(function () {
        Route::group(['middleware' => ['web', 'admin']], function () {
            /*
            * Google Merchant dashboard.
            */
            Route::get('googlemerchant', 'index')->name('admin.googlemerchant.index');
        });

        /*
        * Sync products to Google Merchant Center.
        * GET /admin/google-merchant/sync
        */
        Route::get('google-merchant/sync', 'sync')->name('admin.googlemerchant.sync');
    });
});
