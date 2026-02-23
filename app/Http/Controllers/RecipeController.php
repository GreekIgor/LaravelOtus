<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecipeRequest;
use App\Models\Recipe;
use App\Models\Unit;
use Illuminate\Http\Request;
use App\Services\RecipeService;
use App\Services\IngredientService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class RecipeController extends Controller
{
    protected $recipeService;
    protected $ingredientService;

    public function __construct(RecipeService $recipeService, IngredientService $ingredientService)
    {
        $this->recipeService = $recipeService;
        $this->ingredientService = $ingredientService;
    }


public function showRecipeList(Request $request)
{
        // Начинаем построение запроса
        $query = Recipe::with(['author', 'ingredients']);
        
        // Фильтр по названию
        if ($request->filled('name')) {
            $query->where('title', 'LIKE', '%' . $request->input('name') . '%');
        }
        
        // Фильтр по ингредиентам
        if ($request->filled('ingredients')) {
            $ingredientNames = array_map('trim', explode(',', $request->input('ingredients')));
            $query->whereHas('ingredients', function($q) use ($ingredientNames) {
                $q->where(function($subQuery) use ($ingredientNames) {
                    foreach ($ingredientNames as $name) {
                        $subQuery->orWhere('name', 'LIKE', '%' . $name . '%');
                    }
                });
            });
        }
        
        // Фильтр по сложности
        if ($request->filled('difficulty') && is_array($request->input('difficulty'))) {
            $query->whereIn('difficulty', $request->input('difficulty'));
        }
        
        // Фильтр по времени готовки
        if ($request->filled('time')) {
            $timeFilter = $request->input('time');
            switch ($timeFilter) {
                case 'быстро':
                    $query->where('cooking_time', '<=', 30);
                    break;
                case 'средне':
                    $query->whereBetween('cooking_time', [31, 60]);
                    break;
                case 'долго':
                    $query->where('cooking_time', '>', 60);
                    break;
            }
        }
        
        // Проверяем, есть ли активные фильтры
        $hasFilters = $request->filled('name') || 
                     $request->filled('ingredients') || 
                     ($request->filled('difficulty') && is_array($request->input('difficulty')) && count($request->input('difficulty')) > 0) ||
                     $request->filled('time');
        
        // Добавляем сортировку по умолчанию
        $query->orderBy('created_at', 'desc');
        
        // Кэшируем только если нет фильтров и это первая страница
        $page = $request->get('page', 1);
        if (!$hasFilters && $page == 1) {
            $recipes = Cache::remember('recipes.list.page.1', 300, function () use ($query) {
                return $query->paginate(10)->withQueryString();
            });
        } else {
            // При наличии фильтров или других страницах не кэшируем
            $recipes = $query->paginate(10)->withQueryString();
        }
        
        return view('recipelist', compact('recipes'));
}

public function showRecipe(Recipe $recipe){
   // Используем route model binding - Laravel автоматически резолвит модель
   $recipe = $this->recipeService->getRecipeById($recipe->id);
   return view('Recipe', compact('recipe'));
}


public function showRecipeEdit(Recipe $recipe){
    // Используем route model binding - Laravel автоматически резолвит модель
    $recipe = $this->recipeService->getRecipeById($recipe->id);
    $ingredients = $this->ingredientService->getAllIngredients();
    $units = Unit::all();
    // Используем разрешение edit-own-recipes вместо Policy update
    Gate::authorize('edit-own-recipes', $recipe);
    return view('recipe.edit', compact('recipe', 'ingredients', 'units'));
}


public function showRecipeCreate(){
    $recipe = new Recipe();
    // Используем разрешение create-recipes
    Gate::authorize('create-recipes');
    $ingredients = $this->ingredientService->getAllIngredients();
    $units = Unit::all();
    return view('recipe.edit', compact('recipe', 'ingredients', 'units'));
}

    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
{
    if ($request->ajax()) {
        $draw = $request->get('draw'); // Номер запроса для синхронизации
        $start = $request->get('start'); // С какой записи начать
        $length = $request->get('length'); // Сколько записей взять
        $search = $request->get('search')['value']; // Строка поиска

        // 1. Общее количество записей
        $totalRecords = Recipe::count();

        // 2. Формируем запрос с фильтрацией
        $query = Recipe::with(['author', 'ingredients']);

        if (!empty($search)) {
            $query->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('author', function($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%");
                  });
        }

        // 3. Количество отфильтрованных записей
        $totalRecordwithFilter = $query->count();

        // 4. Получаем данные для текущей страницы
        $recipes = $query->skip($start)
                         ->take($length)
                         ->get();

        // 5. Форматируем данные для таблицы
        $data = $recipes->map(function($recipe) {
            return [
                'id' => $recipe->id,
                'title_info' => $this->renderTitleInfo($recipe),
                'description' => str($recipe->description)->limit(50),
                'author' => $recipe->user->name ?? 'System',
                'ingredients_list' => $recipe->ingredients->take(3)->pluck('name')->implode(', ') . 
                                     ($recipe->ingredients->count() > 3 ? '...' : ''),
                'actions' => $this->renderActions($recipe)
            ];
        });

        return response()->json([
            "draw" => intval($draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecordwithFilter,
            "data" => $data
        ]);
    }

    // Используем разрешение view-admin-dashboard вместо проверки роли
    Gate::authorize('view-admin-dashboard');
    return view('admin.recipes.index');
}

// Вспомогательные методы для чистоты кода
private function renderTitleInfo($recipe) {
    $img = $recipe->image_path ? asset($recipe->image_path) : 'https://via.placeholder.com/50';
    return '<div class="d-flex align-items-center">
                <img src="'.$img.'" class="rounded me-2" style="width:40px;height:40px;object-fit:cover;">
                <div><div class="fw-bold">'.$recipe->title.'</div><small class="text-muted">#'.$recipe->id.'</small></div>
            </div>';
}

private function renderActions($recipe) {
    return '<div class="btn-group">
                <a href="'.route('recipes.edit', $recipe->id).'" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                <button onclick="confirmDelete(\''.route('recipes.destroy', $recipe->id).'\')" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
            </div>';
}
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create-recipes');
        $ingredients = $this->ingredientService->getAllIngredients();
        $units = Unit::all();
        $recipe = new Recipe();
        return view('admin.recipes.edit')->with(compact('ingredients', 'units', 'recipe'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create-recipes');
        $recipe = $this->recipeService->createRecipe(array_merge(['user_id'=>auth()->id()],$request->all()));
        return redirect()->route('recipe-edit', ['recipe' => $recipe->id]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->recipeService->getRecipeById($id);
        return view('recipe.show', compact('recipe'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $recipe = $this->recipeService->getRecipeById($id);
        // Используем разрешение edit-own-recipes
        Gate::authorize('edit-own-recipes', $recipe);
        $ingredients = $this->ingredientService->getAllIngredients();
        $units = Unit::all();
        return view('admin.recipes.edit', compact('recipe', 'ingredients', 'units'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RecipeRequest $request, Recipe $recipe)
    {
        // Используем route model binding - Laravel автоматически резолвит модель
        Gate::authorize('edit-own-recipes', $recipe);
        $this->recipeService->updateRecipe($recipe->id, $request->validated());
        return redirect()->route('recipe-edit', ['recipe' => $recipe->id])->with('success', 'Recipe updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Gate::authorize('delete-recipes');
        $this->recipeService->deleteRecipe($id);
        return redirect()->route('recipes.index')->with('success', 'Recipe deleted successfully.');
    }
}
