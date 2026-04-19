<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentHistoryController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    /**
     * GET /api/v1/payments/history
     */
    public function __invoke(Request $request): JsonResponse
    {
        $payments = $this->paymentService->history($request->user());

        return response()->json([
            'data' => PaymentResource::collection($payments),
            'meta' => [
                'total'        => $payments->total(),
                'current_page' => $payments->currentPage(),
                'last_page'    => $payments->lastPage(),
            ],
        ]);
    }
}
