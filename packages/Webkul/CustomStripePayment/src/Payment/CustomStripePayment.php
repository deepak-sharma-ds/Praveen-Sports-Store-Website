<?php

namespace Webkul\CustomStripePayment\Payment;

use Webkul\Payment\Payment\Payment;

class CustomStripePayment extends Payment
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $code  = 'customstripepayment';

    /**
     * Get redirect url.
     */
    public function getRedirectUrl() {
        return route('stripe.redirect');
    }

    public function getTitle()
    {
        return core()->getConfigData('sales.payment_methods.customstripepayment.title');
    }

    public function getCheckoutView()
    {
        return 'customstripepayment::checkout.payment';
    }

    public function isAvailable()
    {
        return core()->getConfigData('sales.payment_methods.customstripepayment.active') == '1';
    }
}
