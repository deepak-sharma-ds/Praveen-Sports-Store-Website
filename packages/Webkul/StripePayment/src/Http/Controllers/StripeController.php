<?php

namespace Webkul\StripePayment\Http\Controllers;

use Webkul\Checkout\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripeController extends Controller
{
    public function __construct()
    {
        $secret = core()->getConfigData('sales.payment_methods.stripepayment.secret_key');

        if ($secret) {
            Stripe::setApiKey($secret);
        }
    }

    public function createPaymentIntent(Request $request)
    {
        try {
            // Obtain authoritative cart total from Bagisto's cart repository
            // Adjust the following to match your Bagisto version/project
            $cart = Cart::getCart(); // *** replace with your cart call if missing ***

            if (! $cart) {
                return response()->json(['success' => false, 'message' => 'Cart not found'], 400);
            }

            $currency = core()->getCurrentCurrencyCode();

            // Bagisto stores totals in decimal (e.g., 123.45) — convert to minor units
            $amountFloat = (float) $cart->sub_total + $cart->tax_total + ($cart->selected_shipping_rate ? $cart->selected_shipping_rate->price : 0) - $cart->discount_amount;
            // $amountFloat = (float) ($cart->final_total ?? $cart->grand_total ?? 0);
            $amount = (int) round($amountFloat * 100);

            $intent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => strtolower($currency),
                'automatic_payment_methods' => ['enabled' => true],
                'metadata' => [
                    'cart_id' => $cart->id ?? null,
                    'customer_id' => auth()->id() ?? null,
                ],
            ]);

            return response()->json([
                'success' => true,
                'client_secret' => $intent->client_secret,
                'intent_id' => $intent->id,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function confirmPayment(Request $request)
    {
        $paymentIntentId = $request->input('payment_intent_id');

        try {
            $intent = PaymentIntent::retrieve($paymentIntentId);

            if ($intent->status === 'succeeded') {
                // TODO: create Bagisto order here using your order creation logic.
                return response()->json(['success' => true, 'redirect_url' => route('shop.checkout.onepage.success')]);
            }

            return response()->json(['success' => false, 'message' => 'Payment not completed']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function cartAmount()
    {
        $cart = Cart::getCart(); // replace with your project's cart accessor

        if (! $cart) {
            return response()->json(['amount' => 0, 'currency' => core()->getCurrentCurrencyCode()]);
        }

        $currency = core()->getCurrentCurrencyCode();
        $amountFloat = (float) $cart->sub_total + $cart->tax_total + ($cart->selected_shipping_rate ? $cart->selected_shipping_rate->price : 0) - $cart->discount_amount;
        // $amountFloat = (float) ($cart->final_total ?? $cart->grand_total ?? 0);
        $amountMinor = (int) round($amountFloat * 100);

        return response()->json(['amount' => $amountMinor, 'currency' => $currency]);
    }
}
