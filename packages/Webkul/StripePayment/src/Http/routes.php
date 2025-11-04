<?php

use Illuminate\Support\Facades\Route;
use Webkul\StripePayment\Http\Controllers\StripeController;
// Shop routes
Route::group(['middleware' => ['web']], function () {
    Route::post('stripe/create-payment-intent', [StripeController::class, 'createPaymentIntent'])
        ->name('stripe.create-payment-intent');

    Route::post('stripe/confirm-payment', [StripeController::class, 'confirmPayment'])
        ->name('stripe.confirm-payment');

    // Optional: route to return cart amount (minor units)
    Route::get('stripe/cart-amount', [StripeController::class, 'cartAmount'])
        ->name('stripe.cart-amount');
});
