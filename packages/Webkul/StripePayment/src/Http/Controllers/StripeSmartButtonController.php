<?php

namespace Webkul\StripePayment\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Webkul\Checkout\Facades\Cart;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\Sales\Transformers\OrderResource;
use Webkul\StripePayment\Services\StripeSmartButtonService;

class StripeSmartButtonController extends Controller
{
    public function __construct(
        protected StripeSmartButtonService $stripeService,
        protected OrderRepository $orderRepository,
        protected InvoiceRepository $invoiceRepository
    ) {}

    /**
     * Create PaymentIntent for the current cart.
     */
    public function createPaymentIntent(Request $request)
    {
        try {
            $cart = Cart::getCart();

            if (! $cart) {
                return response()->json(['success' => false, 'message' => 'Cart not found.'], 404);
            }

            $this->validateOrder();

            $amount = $this->calculateAmount($cart);

            // $currency = strtolower(core()->getCurrentCurrencyCode());
            $currency = strtolower($cart->cart_currency_code);

            $metadata = $this->getMetaData($cart);
            $shipping = $this->getShippingAddress($cart);

            $paymentIntentArray = [
                'amount' => $amount,
                'currency' => $currency,
                'description' => 'ANA Sports Order #' . $cart->id,
                'metadata' => $metadata,
                'shipping' => $shipping,
                'receipt_email' => $cart->billing_address->email,
                'automatic_payment_methods' => ['enabled' => true],
            ];

            $intent = $this->stripeService->createPaymentIntent($paymentIntentArray);

            return response()->json([
                'success' => true,
                'client_secret' => $intent->client_secret,
                'intent_id' => $intent->id,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Confirm Stripe payment and save order.
     */
    public function confirmPayment(Request $request)
    {
        try {
            $intentId = $request->input('payment_intent_id');

            if (! $intentId) {
                return response()->json(['success' => false, 'message' => 'Missing payment_intent_id.'], 400);
            }

            $intent = $this->stripeService->retrievePaymentIntent($intentId);

            if ($intent->status !== 'succeeded') {
                return response()->json(['success' => false, 'message' => 'Payment not completed.'], 400);
            }

            // Create order after payment success
            $order = $this->saveOrder();

            return response()->json([
                'success' => true,
                'redirect_url' => route('shop.checkout.onepage.success'),
                'order_id' => $order->id,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Return current cart amount in minor units.
     */
    public function cartAmount()
    {
        $cart = Cart::getCart();

        if (! $cart) {
            return response()->json(['amount' => 0]);
        }

        return response()->json([
            'amount' => $this->calculateAmount($cart),
            'currency' => core()->getCurrentCurrencyCode(),
        ]);
    }

    /**
     * Calculate Stripe-friendly amount in minor units (cents/paise).
     */
    protected function calculateAmount($cart): int
    {
        $total = (float) $cart->sub_total + $cart->tax_total + ($cart->selected_shipping_rate->price ?? 0) - $cart->discount_amount;

        return (int) round($total * 100);
    }

    /**
     * Create Bagisto order + invoice after payment.
     */
    protected function saveOrder()
    {
        if (Cart::hasError()) {
            return response()->json(['redirect_url' => route('shop.checkout.cart.index')], 403);
        }

        Cart::collectTotals();

        $cart = Cart::getCart();
        $data = (new OrderResource($cart))->jsonSerialize();

        $order = $this->orderRepository->create($data);
        $this->orderRepository->update(['status' => 'processing'], $order->id);

        if ($order->canInvoice()) {
            $this->invoiceRepository->create($this->prepareInvoiceData($order));
        }

        Cart::deActivateCart();

        session()->flash('order_id', $order->id);

        return $order;
    }

    /**
     * Prepare order invoice data.
     */
    protected function prepareInvoiceData($order)
    {
        $invoiceData = ['order_id' => $order->id];

        foreach ($order->items as $item) {
            $invoiceData['invoice']['items'][$item->id] = $item->qty_to_invoice;
        }

        return $invoiceData;
    }

    /**
     * Validate order before creation (same logic as PayPal SmartButton).
     */
    protected function validateOrder()
    {
        $cart = Cart::getCart();

        $minimumOrderAmount = (float) core()->getConfigData('sales.order_settings.minimum_order.minimum_order_amount') ?: 0;

        if (! Cart::haveMinimumOrderAmount()) {
            throw new \Exception(trans('shop::app.checkout.cart.minimum-order-message', [
                'amount' => core()->currency($minimumOrderAmount)
            ]));
        }

        if ($cart->haveStockableItems() && ! $cart->shipping_address) {
            throw new \Exception(trans('shop::app.checkout.cart.check-shipping-address'));
        }

        if (! $cart->billing_address) {
            throw new \Exception(trans('shop::app.checkout.cart.check-billing-address'));
        }

        if ($cart->haveStockableItems() && ! $cart->selected_shipping_rate) {
            throw new \Exception(trans('shop::app.checkout.cart.specify-shipping-method'));
        }

        if (! $cart->payment) {
            throw new \Exception(trans('shop::app.checkout.cart.specify-payment-method'));
        }
    }

    /**
     * Return cart meta keys array.
     *
     * @param  string  $cart
     * @return array
     */
    protected function getMetaData($cart)
    {
        // ✅ Use Stripe official metadata keys
        $metadata = [
            'cart_id' => $cart->id,
            'customer_id' => auth()->id() ?? 'guest',
            'currency' => strtolower($cart->cart_currency_code),
            'subtotal' => (string) $cart->sub_total,
            'tax_total' => (string) $cart->tax_total,
            'shipping_amount' => (string) ($cart->selected_shipping_rate->price ?? 0),
            'discount' => (string) $cart->discount_amount,
            'total' => (string) $amountFloat,
            'item_count' => (string) $cart->items->count(),
        ];

        // ✅ Add line-item metadata (up to Stripe’s 50-field limit)
        foreach ($cart->items as $index => $item) {
            if ($index >= 10) break; // Stripe recommends small metadata objects
            $metadata["item_{$index}_name"] = $item->name;
            $metadata["item_{$index}_sku"] = $item->sku;
            $metadata["item_{$index}_qty"] = (string) $item->quantity;
            $metadata["item_{$index}_price"] = (string) $item->price;
        }

        return $metadata;
    }

    /**
     * Return Shipping address array.
     *
     * @param  string  $cart
     * @return array
     */
    protected function getShippingAddress($cart)
    {
        // ✅ Build Stripe-compliant shipping object
        $shipping = [];
        if ($cart->haveStockableItems() && $cart->shipping_address) {
            $shippingAddressLines = $this->getAddressLines($cart->shipping_address->address);
            $shipping = [
                'name' => $cart->shipping_address->first_name . ' ' . $cart->shipping_address->last_name,
                'address' => [
                    'line1' => current($shippingAddressLines),
                    'line2' => last($shippingAddressLines),
                    'city' => $cart->shipping_address->city,
                    'state' => $cart->shipping_address->state,
                    'postal_code' => $cart->shipping_address->postcode,
                    'country' => $cart->shipping_address->country,
                ],
                'phone' => $cart->shipping_address->phone ?? null,
            ];
        }
        return $shipping;
    }

    /**
     * Return Billing address array.
     *
     * @param  string  $cart
     * @return array
     */
    protected function getBillingAddress($cart) {}

    /**
     * Return convert multiple address lines into 2 address lines.
     *
     * @param  string  $address
     * @return array
     */
    protected function getAddressLines($address)
    {
        $address = explode(PHP_EOL, $address, 2);

        $addressLines = [current($address)];

        if (isset($address[1])) {
            $addressLines[] = str_replace(["\r\n", "\r", "\n"], ' ', last($address));
        } else {
            $addressLines[] = '';
        }

        return $addressLines;
    }
}
