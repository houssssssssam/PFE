<?php

namespace App\Http\Controllers\Api\V1\Expert;

use App\Http\Controllers\Controller;
use App\Http\Resources\WalletResource;
use App\Http\Resources\WalletTransactionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpertWalletController extends Controller
{
    /**
     * Get expert wallet overview.
     *
     * GET /api/v1/expert/wallet
     */
    public function show(Request $request): JsonResponse
    {
        $expert = $request->user()->expert;

        if (! $expert) {
            return response()->json(['message' => 'Profil expert introuvable.'], 404);
        }

        $wallet = $expert->wallet;

        if (! $wallet) {
            return response()->json([
                'data' => [
                    'balance'         => '0.00',
                    'total_earned'    => '0.00',
                    'total_withdrawn' => '0.00',
                    'transactions'    => [],
                ],
            ]);
        }

        return response()->json([
            'data' => new WalletResource($wallet),
        ]);
    }

    /**
     * Get expert wallet transactions with pagination.
     *
     * GET /api/v1/expert/wallet/transactions
     */
    public function transactions(Request $request): JsonResponse
    {
        $expert = $request->user()->expert;

        if (! $expert || ! $expert->wallet) {
            return response()->json([
                'data' => [],
                'meta' => ['total' => 0],
            ]);
        }

        $transactions = $expert->wallet
            ->transactions()
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'data' => WalletTransactionResource::collection($transactions),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page'    => $transactions->lastPage(),
                'per_page'     => $transactions->perPage(),
                'total'        => $transactions->total(),
            ],
        ]);
    }
}
