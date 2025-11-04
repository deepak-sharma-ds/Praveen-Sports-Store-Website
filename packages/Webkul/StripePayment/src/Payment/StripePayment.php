<?php

namespace Webkul\StripePayment\Payment;

use Webkul\Payment\Payment\Payment;

class StripePayment extends Payment
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $code  = 'stripepayment';

    /**
     * Get redirect url.
     */
    public function getRedirectUrl()
    {
    }
}
