<?php

namespace App\Repositories;

use App\Models\Recipe;

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

    public function create(array $data): Recipe
    {
        return Recipe::create($data);
    }

    public function update($id, array $data): bool
    {
        $recipe = $this->findById($id);
        if ($recipe) {
            return $recipe->update($data);
        }
        return false;
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