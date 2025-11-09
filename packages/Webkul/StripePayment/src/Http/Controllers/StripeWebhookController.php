<?php

namespace Webkul\StripePayment\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Webkul\StripePayment\Services\StripeSmartButtonService;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Repositories\InvoiceRepository;

class StripeWebhookController extends Controller
{
    public function __construct(
        protected StripeSmartButtonService $stripeService,
        protected OrderRepository $orderRepository,
        protected InvoiceRepository $invoiceRepository
    ) {}

    public function handle(Request $request)
    {
        $secret = config('services.stripe.webhook_secret');

        $signature = $request->header('Stripe-Signature');
        $payload = $request->getContent();

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $signature, $secret);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $intent = $event->data->object;
            \Log::info('Stripe webhook payment succeeded', ['id' => $intent->id]);

            // Optionally auto-create order here (similar to confirmPayment flow)
        }

        return response()->json(['received' => true]);
    }
}
