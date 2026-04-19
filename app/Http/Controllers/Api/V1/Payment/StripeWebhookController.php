<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StripeWebhookController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    /**
     * POST /api/v1/webhooks/stripe
     */
    public function __invoke(Request $request): Response
    {
        $signature = $request->header('Stripe-Signature', '');

        try {
            $this->paymentService->handleWebhook($request->getContent(), $signature);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        } catch (\Throwable $e) {
            return response('Webhook error: ' . $e->getMessage(), 500);
        }

        return response('OK', 200);
    }
}
