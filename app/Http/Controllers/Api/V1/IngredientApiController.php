<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\IngredientResource;
use App\Models\Ingredient;
use Illuminate\Http\JsonResponse;

class IngredientApiController extends Controller
{
    /**
     * GET /api/v1/ingredients
     * Получить список всех ингредиентов
     */
    public function index(): JsonResponse
    {
        $ingredients = Ingredient::with('unit:id,name')
            ->select('ingredients.*')
            ->get();

        return IngredientResource::collection($ingredients)->response();
    }
}
