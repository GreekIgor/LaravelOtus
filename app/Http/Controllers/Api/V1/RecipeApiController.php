<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RecipeRequest;
use App\Http\Resources\RecipeResource;
use App\Models\Recipe;
use App\Services\RecipeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RecipeApiController extends Controller
{
    public function __construct(
        protected RecipeService $recipeService
    ) {
    }

    /**
     * GET /api/v1/recipes
     */
    public function index(Request $request): JsonResponse
    {
        $query = Recipe::with([
            'author:id,name',
            'ingredients:id,name'
        ])->select('recipes.*');

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->string('title')->toString() . '%');
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->string('difficulty')->toString());
        }

        if ($request->filled('time_max')) {
            $query->where('cooking_time', '<=', (int) $request->input('time_max'));
        }

        $perPage = (int) $request->input('per_page', 10);
        $recipes = $query->orderByDesc('created_at')->paginate($perPage);

        return RecipeResource::collection($recipes)->response();
    }

    /**
     * GET /api/v1/recipes/{id}
     */
    public function show(Recipe $recipe): RecipeResource
    {
        $recipe->load([
            'author:id,name',
            'ingredients:id,name'
        ]);

        return new RecipeResource($recipe);
    }

    /**
     * POST /api/v1/recipes
     */
    public function store(RecipeRequest $request): JsonResponse
    {
        Gate::authorize('create-recipes');

        $recipe = $this->recipeService->createRecipe($request->all());

        return (new RecipeResource($recipe))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * PUT/PATCH /api/v1/recipes/{id}
     */
    public function update(RecipeRequest $request, Recipe $recipe): RecipeResource
    {
        Gate::authorize('edit-own-recipes', $recipe);

        $updated = $this->recipeService->updateRecipe($recipe->id, $request->all());

        return new RecipeResource($updated);
    }

    /**
     * DELETE /api/v1/recipes/{id}
     */
    public function destroy(Recipe $recipe): JsonResponse
    {
        Gate::authorize('delete-recipes');

        $this->recipeService->deleteRecipe($recipe->id);

        return response()->json(null, 204);
    }
}

