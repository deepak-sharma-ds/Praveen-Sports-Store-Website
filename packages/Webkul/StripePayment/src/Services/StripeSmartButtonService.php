<?php

namespace Webkul\StripePayment\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripeSmartButtonService
{
    public function __construct()
    {
        $secret = core()->getConfigData('sales.payment_methods.stripepayment.secret_key');

        if ($secret) {
            Stripe::setApiKey($secret);
        }
    }

    /**
     * Create a Stripe PaymentIntent.
     */
    public function createPaymentIntent(array $paymentIntentArray = [])
    {
        return PaymentIntent::create($paymentIntentArray);
    }

    /**
     * Retrieve an existing PaymentIntent.
     */
    public function retrievePaymentIntent(string $id)
    {
        return PaymentIntent::retrieve($id);
    }
}
