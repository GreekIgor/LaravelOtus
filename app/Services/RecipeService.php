<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Unit;
use App\Repositories\RecipeRepository;
use DB;
use Illuminate\Support\Facades\Auth;

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
        return $this->recipeRepository->getAllRecipes();
    }
    public function getRecipeById($id)
    {
        return $this->recipeRepository->findById($id);
    }
public function createRecipe(array $recipeData)
{
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
    $allowedFields = ['title', 'time', 'difficulty', 'instructions', 'image_path', 'user_id'];
    
    // Добавляем ID текущего пользователя
    $recipeData['user_id'] = Auth::id();
    // Фильтруем массив, чтобы убрать 'ingredients', 'amounts' и 'units'
    $filteredRecipeData = array_intersect_key($recipeData, array_flip($allowedFields));

    return $this->recipeRepository->create($filteredRecipeData, $syncData);
}
public function updateRecipe($id, array $data): Recipe
{
    $recipe = Recipe::findOrFail($id);

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

    
     return $this->recipeRepository->update(
        $recipe, 
        $data, 
        $syncData
    );
}
    public function deleteRecipe($id)
    {
        return $this->recipeRepository->delete($id);
    }
    
}