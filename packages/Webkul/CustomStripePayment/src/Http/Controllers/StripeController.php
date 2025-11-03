<?php

namespace Webkul\CustomStripePayment\Http\Controllers;

use Webkul\Checkout\Facades\Cart;
use Webkul\Sales\Repositories\OrderRepository;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripeController extends Controller
{
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    // 1️⃣ Create Payment Intent
    public function createPaymentIntent()
    {
        try {
            Stripe::setApiKey(core()->getConfigData('sales.payment_methods.customstripepayment.secret_key'));

            $amount = Cart::getCart()->grand_total * 100; // convert INR to paisa

            $intent = PaymentIntent::create([
                'amount' => (int) $amount,
                'currency' => 'inr',
                'payment_method_types' => ['card'], // wallets included
            ]);

            return response()->json([
                'clientSecret' => $intent->client_secret,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    // ✅ 2️⃣ After Payment Success → Create Order
    public function captureOrder(Request $request)
    {
        if (!Cart::getCart()) {
            return response()->json(['error' => 'Cart not found'], 400);
        }

        $order = $this->orderRepository->create(Cart::prepareDataForOrder());
        Cart::deActivateCart();

        return response()->json([
            'success' => true,
            'orderId' => $order->id,
            'redirectUrl' => route('shop.checkout.success', $order->id)
        ]);
    }



    public function createIntent()
    {
        Stripe::setApiKey(core()->getConfigData('sales.payment_methods.customstripepayment.secret_key'));

        $amount = round(Cart::getCart()->base_grand_total * 100);

        $paymentIntent = PaymentIntent::create([
            'amount' => $amount,
            'currency' => 'inr',
            'automatic_payment_methods' => ['enabled' => true],
        ]);

        return response()->json(['client_secret' => $paymentIntent->client_secret]);
    }

    public function completeOrder(Request $request)
    {
        app(OrderRepository::class)->create(Cart::prepareDataForOrder());

        Cart::deactivateCurrentCart();

        return response()->json(['success' => true]);
    }
}
