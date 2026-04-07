<?php

use Illuminate\Support\Facades\Route;
use Webkul\Brochure\Http\Controllers\Shop\BrochureController;
use Webkul\Brochure\Http\Controllers\Shop\BrochureViewController;

Route::group(['middleware' => ['web', 'theme', 'locale', 'currency']], function () {
    // Brochure listing page
    Route::get('brochures', [BrochureController::class, 'index'])->name('shop.brochure.index');

    // Brochure flipbook viewer (supports ?page=N deep linking)
    Route::get('brochure/{slug}', [BrochureViewController::class, 'show'])->name('shop.brochure.view');
});
