<?php

use Illuminate\Support\Facades\Route;
use Webkul\StripePayment\Http\Controllers\StripeController;
use Webkul\StripePayment\Http\Controllers\StripeSmartButtonController;
use Webkul\StripePayment\Http\Controllers\StripeWebhookController;

// Shop routes
// Route::group(['middleware' => ['web']], function () {
//     Route::post('stripe/create-payment-intent', [StripeController::class, 'createPaymentIntent'])
//         ->name('stripe.create-payment-intent');

//     Route::post('stripe/confirm-payment', [StripeController::class, 'confirmPayment'])
//         ->name('stripe.confirm-payment');

//     // Optional: route to return cart amount (minor units)
//     Route::get('stripe/cart-amount', [StripeController::class, 'cartAmount'])
//         ->name('stripe.cart-amount');
// });

Route::group(['middleware' => ['web', 'theme', 'locale', 'currency']], function () {
    Route::prefix('stripe')->group(function () {
        Route::post('create-payment-intent', [StripeSmartButtonController::class, 'createPaymentIntent'])->name('stripe.create-payment-intent');
        Route::post('confirm-payment', [StripeSmartButtonController::class, 'confirmPayment'])->name('stripe.confirm-payment');
        Route::get('cart-amount', [StripeSmartButtonController::class, 'cartAmount'])->name('stripe.cart-amount');
        Route::post('webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');
    });
});
