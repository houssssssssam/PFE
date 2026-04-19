<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CmiCallbackController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    /**
     * POST /api/v1/payments/cmi/callback
     * Public endpoint — called by CMI server after payment.
     */
    public function __invoke(Request $request): Response
    {
        $action = $this->paymentService->handleCmiCallback($request->all());

        return response($action, 200)->header('Content-Type', 'text/plain');
    }
}
