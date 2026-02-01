<?php

namespace App\Repositories;

use App\Models\Ingredient;
use App\Models\Recipe;
use DB;

class RecipeRepository
{
    // Repository implementation
    public function getAllRecipes()
    {
        return Recipe::with(['author', 'ingredients'])->get();
    }

    public function findById($id): ?Recipe
    {
        return Recipe::with(['author', 'ingredients'])->find($id);
    }

public function create(array $recipeData, array $syncData): Recipe
{
    return DB::transaction(function() use ($recipeData, $syncData) {
        $recipe = Recipe::create($recipeData);

        if (!empty($syncData)) {
            $recipe->ingredients()->attach($syncData);
        }

        return $recipe->fresh(['ingredients']);
    });
}

public function update(Recipe $recipe, array $recipeData, array $syncIngredients): Recipe
{
    return DB::transaction(function () use ($recipe, $recipeData, $syncIngredients) {
        //  Простое обновление полей самой таблицы recipes
        $recipe->update($recipeData);

        //  Синхронизация связей (sync ожидает массив [id => ['pivot_field' => 'value']])
        if (!empty($syncIngredients)) {
            $recipe->ingredients()->sync($syncIngredients);
        }

        return $recipe->fresh(['ingredients']); // Возвращаем обновленный объект с загруженными связями
    });
}

    public function delete($id): bool|null
    {
        $recipe = $this->findById($id);
        if ($recipe) {
            return $recipe->delete();
        }
        return null;
    }
}