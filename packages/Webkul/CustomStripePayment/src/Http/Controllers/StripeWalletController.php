<?php

namespace Webkul\CustomStripePayment\Http\Controllers;

use App\Http\Controllers\Controller;
use Webkul\Checkout\Facades\Cart;
use Webkul\Sales\Repositories\OrderRepository;
use Illuminate\Http\Request;
use Stripe\StripeClient;

class StripeWalletController extends Controller
{
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function redirect()
    {
        return view('customstripepayment::checkout.payment');
    }

    public function process(Request $request)
    {
        $cart = Cart::getCart();

        $stripe = new StripeClient(
            core()->getConfigData('sales.payment_methods.customstripepayment.secret_key')
        );

        $paymentIntent = $stripe->paymentIntents->create([
            'amount' => round($cart->grand_total * 100),
            'currency' => core()->getCurrentCurrencyCode(),
            'payment_method' => $request->payment_method,
            'confirmation_method' => 'automatic',
            'confirm' => true,
            'description' => "Order Payment",
        ]);

        $order = $this->orderRepository->create(Cart::prepareDataForOrder());

        Cart::deActivateCart();

        return response()->json([
            'success' => true,
            'redirect_url' => route('shop.checkout.success'),
        ]);
    }
}
