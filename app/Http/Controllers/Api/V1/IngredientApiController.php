<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\IngredientResource;
use App\Models\Ingredient;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class IngredientApiController extends Controller
{
    /**
     * GET /api/v1/ingredients
     * Получить список всех ингредиентов
     */
    public function index(): JsonResponse
    {
        $cacheKey = 'api.ingredients.list.' . md5(request()->getSchemeAndHttpHost());

        $cached = Cache::get($cacheKey);
        if (is_array($cached)) {
            return response()->json($cached);
        }

        $ingredients = Ingredient::with('unit:id,name')
            ->select('ingredients.*')
            ->get();

        $payload = IngredientResource::collection($ingredients)->response()->getData(true);

        Cache::put($cacheKey, $payload, now()->addMinutes(10));

        return response()->json($payload);
    }
}
