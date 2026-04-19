<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Expert;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CmiInitiateController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    /**
     * POST /api/v1/payments/cmi/initiate
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

        $result = $this->paymentService->initiateCmi($request->user(), $expert, $conversation);

        return response()->json(['data' => $result]);
    }
}
