<?php

namespace App\Services;

use App\Events\RecipeCreated;
use App\Events\RecipeUpdated;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Tag;
use App\Models\Unit;
use App\Repositories\RecipeRepository;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class RecipeService
{
    // Recipe service methods would go here
    protected $recipeRepository;

    public function __construct(RecipeRepository $recipeRepository)
    {
        $this->recipeRepository = $recipeRepository;
    }

    public function getAllRecipes()
    {
        return Cache::remember('recipes.all', 600, function () {
            return $this->recipeRepository->getAllRecipes();
        });
    }
    
    public function getRecipeById($id)
    {
        return Cache::remember("recipe.{$id}", 1800, function () use ($id) {
            return $this->recipeRepository->findById($id);
        });
    }
public function createRecipe(array $recipeData)
{
    $recipeData['difficulty'] = $this->normalizeDifficulty($recipeData['difficulty'] ?? null);

    // Используем 'image_path' или 'img' в зависимости от вашей базы
    if (isset($recipeData['image']) && $recipeData['image'] instanceof \Illuminate\Http\UploadedFile) {
        $recipeData['image_path'] = $recipeData['image']->store('recipes', 'public');
    }

    $syncData = [];
    if (isset($recipeData['ingredients']) && is_array($recipeData['ingredients'])) {
        foreach ($recipeData['ingredients'] as $index => $ingredientId) {
            if (empty($ingredientId)) continue;

            $syncData[$ingredientId] = [
                'quantity'  => $recipeData['amounts'][$index] ?? 0
            ];
        }
    }
    // Оставляем только те ключи, которые есть в столбцах таблицы БД
    if (empty($recipeData['tag_id'])) {
        $recipeData['tag_id'] = Tag::query()->value('id');
    }

    $allowedFields = ['title', 'description', 'cooking_time', 'difficulty', 'instructions', 'image_path', 'user_id', 'tag_id'];
    
    // Добавляем ID текущего пользователя
    $recipeData['user_id'] = Auth::id();
    // Фильтруем массив, чтобы убрать 'ingredients', 'amounts' и 'units'
    $filteredRecipeData = array_intersect_key($recipeData, array_flip($allowedFields));

    $recipe = $this->recipeRepository->create($filteredRecipeData, $syncData);
    
    // Загружаем отношения для события
    $recipe->load('author');
    
    // Публикуем событие в очередь RabbitMQ
    event(new RecipeCreated($recipe));
    
    // Очистка кэша после создания рецепта
    $this->clearRecipeCache();
    
    return $recipe;
}
public function updateRecipe($id, array $data): Recipe
{
    $recipe = Recipe::findOrFail($id);

    $data['difficulty'] = $this->normalizeDifficulty($data['difficulty'] ?? null);

    if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
        $data['img'] = $data['image']->store('recipes', 'public');
    }

    $syncData = [];
    
    if (isset($data['ingredients']) && is_array($data['ingredients'])) {
        foreach ($data['ingredients'] as $index => $ingredientId) {
            // Пропускаем, если ID ингредиента не выбран (пустой шаблон)
            if (empty($ingredientId)) continue;

            $syncData[$ingredientId] = [
                'amount' => $data['amounts'][$index] ?? 0,
                'unit_id' => $data['units'][$index] ?? null,
            ];
        }
    }

    
    $allowedFields = ['title', 'description', 'cooking_time', 'difficulty', 'instructions', 'image_path', 'user_id', 'tag_id'];
    $filteredData = array_intersect_key($data, array_flip($allowedFields));

    $updatedRecipe = $this->recipeRepository->update(
        $recipe, 
        $filteredData, 
        $syncData
    );
    
    // Загружаем отношения для события
    $updatedRecipe->load('author');
    
    // Публикуем событие в очередь RabbitMQ
    event(new RecipeUpdated($updatedRecipe));
    
    // Очистка кэша после обновления рецепта
    $this->clearRecipeCache($id);
    
    return $updatedRecipe;
}

    private function normalizeDifficulty(mixed $difficulty): string
    {
        if (! is_string($difficulty)) {
            return 'легкий';
        }

        $value = mb_strtolower(trim($difficulty));

        return match ($value) {
            'легкий', 'easy' => 'легкий',
            'средний', 'medium' => 'средний',
            'тяжелый', 'hard' => 'тяжелый',
            default => 'легкий',
        };
    }
    public function deleteRecipe($id)
    {
        $result = $this->recipeRepository->delete($id);
        
        // Очистка кэша после удаления рецепта
        $this->clearRecipeCache($id);
        
        return $result;
    }
    
    /**
     * Очистка кэша рецептов
     */
    protected function clearRecipeCache(?int $recipeId = null): void
    {
        // Очистка общего списка рецептов
        Cache::forget('recipes.all');
        
        // Очистка конкретного рецепта, если указан ID
        if ($recipeId) {
            Cache::forget("recipe.{$recipeId}");
        }
        
        // Очистка кэша списка рецептов с пагинацией (все страницы)
        // Используем простой подход - очищаем первые 10 страниц
        for ($page = 1; $page <= 10; $page++) {
            Cache::forget("recipes.list.page.{$page}");
        }
        
        // Очистка статистики админ-панели
        Cache::forget('admin.stats');
        Cache::forget('admin.top_authors');
    }
    
}