<?php

use Illuminate\Support\Facades\Route;
use Webkul\CustomStripePayment\Http\Controllers\StripeController;
use Webkul\CustomStripePayment\Http\Controllers\StripeWalletController;

Route::get('/redirect', [StripeWalletController::class, 'redirect'])->name('stripe.redirect');

Route::post('/stripe/wallet/process', [StripeWalletController::class, 'process'])
    ->name('stripe.wallet.process');

// Route::group(['middleware' => ['web']], function () {
//     Route::post('/stripe/payment-intent', [StripeController::class, 'createPaymentIntent'])->name('stripe.intent');
//     Route::post('/stripe/capture-order', [StripeController::class, 'captureOrder'])->name('stripe.captureOrder');
// });
Route::post('stripe/payment-intent', [StripeController::class, 'createIntent'])
    ->name('stripe.payment.intent');
Route::post('stripe/order-complete', [StripeController::class, 'completeOrder'])
    ->name('stripe.order.complete');
