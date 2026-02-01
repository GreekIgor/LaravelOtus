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


public function showRecipeList()
{
        $page = request()->get('page', 1);
        
        // Кэшируем первую страницу на 5 минут
        if ($page == 1) {
            $recipes = Cache::remember('recipes.list.page.1', 300, function () {
                return Recipe::with(['author', 'ingredients'])->paginate(10);
            });
        } else {
            // Для остальных страниц не кэшируем (можно добавить при необходимости)
            $recipes = Recipe::with(['author', 'ingredients'])->paginate(10);
        }
        
        return view('recipelist', compact('recipes'));
}

public function showRecipe(int $recipeID){

   $recipe = $this->recipeService->getRecipeById($recipeID);
   return view('Recipe', compact('recipe'));
}


public function showRecipeEdit(int $recipeID){
    $recipe = $this->recipeService->getRecipeById($recipeID);
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
        $locale = app()->getLocale();
        return redirect()->route('recipe-edit', ['locale' => $locale, 'recipe' => $recipe->id]);
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
    public function update(RecipeRequest $request, string $id)
    {
        $recipe = $this->recipeService->getRecipeById($id);
        Gate::authorize('edit-own-recipes', $recipe);
        $this->recipeService->updateRecipe($id, $request->validated());
        $locale = app()->getLocale();
        return redirect()->route('recipe-edit', ['locale' => $locale, 'recipe' => $id])->with('success', 'Recipe updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Gate::authorize('delete-recipes');
        $this->recipeService->deleteRecipe($id);
        $locale = app()->getLocale();
        return redirect()->route('recipes.index', ['locale' => $locale])->with('success', 'Recipe deleted successfully.');
    }
}
