<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Handle Stripe webhook events.
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        if (!$webhookSecret) {
            Log::warning('Stripe webhook secret not configured');
            return response()->json(['error' => 'Webhook secret not configured'], 500);
        }

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $webhookSecret
            );
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe webhook: Invalid payload', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook: Invalid signature', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        try {
            $this->paymentService->handleStripeWebhook($event->toArray());
        } catch (\Exception $e) {
            Log::error('Stripe webhook: Error processing event', [
                'event_type' => $event->type,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Error processing webhook'], 500);
        }

        return response()->json(['received' => true]);
    }
}
