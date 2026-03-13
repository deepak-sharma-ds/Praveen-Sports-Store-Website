<?php

use Illuminate\Support\Facades\Route;
use Webkul\GoogleMerchant\Http\Controllers\Shop\GoogleMerchantController;

Route::group(['middleware' => ['web', 'theme', 'locale', 'currency'], 'prefix' => 'googlemerchant'], function () {
    Route::get('', [GoogleMerchantController::class, 'index'])->name('shop.googlemerchant.index');
});