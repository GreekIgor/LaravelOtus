<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RecipeRequest;
use App\Http\Resources\RecipeResource;
use App\Models\Recipe;
use App\Services\RecipeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
        $perPage = max(1, min((int) $request->input('per_page', 10), 50));
        $page = max(1, (int) $request->input('page', 1));

        $cacheKey = 'api.recipes.' . md5(json_encode([
            'host' => $request->getSchemeAndHttpHost(),
            'title' => $request->input('title'),
            'difficulty' => $request->input('difficulty'),
            'time_max' => $request->input('time_max'),
            'per_page' => $perPage,
            'page' => $page,
        ]));

        $cached = Cache::get($cacheKey);
        if (is_array($cached)) {
            return response()->json($cached);
        }

        $query = Recipe::with([
            'author:id,name',
            'ingredients:id,name'
        ])->select([
            'recipes.id',
            'recipes.title',
            'recipes.difficulty',
            'recipes.cooking_time',
            'recipes.image_path',
            'recipes.user_id',
            'recipes.created_at',
            'recipes.updated_at',
        ]);

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->string('title')->toString() . '%');
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->string('difficulty')->toString());
        }

        if ($request->filled('time_max')) {
            $query->where('cooking_time', '<=', (int) $request->input('time_max'));
        }

        $recipes = $query->orderByDesc('created_at')->paginate($perPage);
        $payload = RecipeResource::collection($recipes)->response()->getData(true);

        Cache::put($cacheKey, $payload, now()->addSeconds(45));

        return response()->json($payload);
    }

    /**
     * GET /api/v1/recipes/{id}
     */
    public function show(Request $request, Recipe $recipe): JsonResponse
    {
        $cacheKey = 'api.recipe.show.' . md5(json_encode([
            'host' => $request->getSchemeAndHttpHost(),
            'id' => $recipe->id,
        ]));

        $cached = Cache::get($cacheKey);
        if (is_array($cached)) {
            return response()->json($cached);
        }

        $recipe->load([
            'author:id,name',
            'ingredients:id,name'
        ]);

        $payload = (new RecipeResource($recipe))->response()->getData(true);

        Cache::put($cacheKey, $payload, now()->addSeconds(60));

        return response()->json($payload);
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

