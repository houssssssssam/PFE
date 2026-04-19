<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentConfirmController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    /**
     * POST /api/v1/payments/stripe/confirm
     */
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'payment_intent_id' => ['required', 'string'],
        ]);

        $payment = $this->paymentService->confirm($request->payment_intent_id);

        return response()->json([
            'message' => 'Paiement confirmé.',
            'data'    => new PaymentResource($payment->load(['user', 'expert.user'])),
        ]);
    }
}
