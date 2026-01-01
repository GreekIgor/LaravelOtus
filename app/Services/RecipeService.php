<?php

namespace App\Services;

use App\Repositories\RecipeRepository;

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
    public function createRecipe(array $data)
    {
        return $this->recipeRepository->create($data);
    }
    public function updateRecipe($id, array $data)
    {
        return $this->recipeRepository->update($id, $data);
    }
    public function deleteRecipe($id)
    {
        return $this->recipeRepository->delete($id);
    }
    
}