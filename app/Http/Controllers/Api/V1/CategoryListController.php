<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryListController extends Controller
{
    /**
     * List all active categories.
     *
     * GET /api/v1/categories
     */
    public function __invoke(): JsonResponse
    {
        $categories = Category::active()
            ->withCount(['experts' => fn ($q) => $q->where('status', 'validated')])
            ->get();

        return response()->json([
            'data' => CategoryResource::collection($categories),
        ]);
    }
}
