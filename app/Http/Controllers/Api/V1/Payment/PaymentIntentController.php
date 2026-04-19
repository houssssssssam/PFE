<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Expert;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentIntentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    /**
     * POST /api/v1/payments/stripe/intent
     */
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'expert_id'       => ['required', 'exists:experts,id'],
            'conversation_id' => ['required', 'exists:conversations,id'],
        ]);

        $expert       = Expert::findOrFail($request->expert_id);
        $conversation = Conversation::findOrFail($request->conversation_id);

        if ($conversation->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }

        if (! $expert->hourly_rate) {
            return response()->json(['message' => "Cet expert n'a pas défini de tarif."], 422);
        }

        $result = $this->paymentService->createIntent($request->user(), $expert, $conversation);

        return response()->json(['data' => $result]);
    }
}
