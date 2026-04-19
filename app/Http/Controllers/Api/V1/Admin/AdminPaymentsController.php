<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Enums\PaymentStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminPaymentsController extends Controller
{
    /**
     * GET /api/v1/admin/payments
     */
    public function __invoke(Request $request): JsonResponse
    {
        $payments = Payment::query()
            ->with(['user', 'expert.user', 'conversation'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->provider, fn ($q) => $q->where('provider', $request->provider))
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total_revenue'   => Payment::where('status', PaymentStatus::Completed)->sum('amount'),
            'stripe_revenue'  => Payment::where('status', PaymentStatus::Completed)->where('provider', 'stripe')->sum('amount'),
            'cmi_revenue'     => Payment::where('status', PaymentStatus::Completed)->where('provider', 'cmi')->sum('amount'),
            'pending_count'   => Payment::where('status', PaymentStatus::Pending)->count(),
            'completed_count' => Payment::where('status', PaymentStatus::Completed)->count(),
            'failed_count'    => Payment::where('status', PaymentStatus::Failed)->count(),
        ];

        return response()->json([
            'data' => PaymentResource::collection($payments),
            'meta' => [
                'total'        => $payments->total(),
                'current_page' => $payments->currentPage(),
                'last_page'    => $payments->lastPage(),
            ],
            'stats' => $stats,
        ]);
    }
}
