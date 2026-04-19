<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Expert;
use App\Models\Payment;
use App\Models\User;
use App\Enums\ConversationStatus;
use App\Enums\ExpertStatus;
use App\Enums\PaymentStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * GET /api/v1/admin/dashboard
     */
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json([
            'data' => [
                'total_users'            => User::count(),
                'total_experts'          => Expert::where('status', ExpertStatus::Validated)->count(),
                'pending_experts'        => Expert::where('status', ExpertStatus::Pending)->count(),
                'total_conversations'    => Conversation::count(),
                'active_conversations'   => Conversation::whereIn('status', [
                    ConversationStatus::Ai,
                    ConversationStatus::Expert,
                ])->count(),
                'total_revenue'          => Payment::where('status', PaymentStatus::Completed)->sum('amount'),
            ],
        ]);
    }
}
