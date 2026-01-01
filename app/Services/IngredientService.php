<?php

namespace App\Services;
use App\Models\Ingredient;
use App\Repositories\IngredientRepository;
use Illuminate\Database\Eloquent\Collection;
use Storage;

class IngredientService
{
    protected $ingredientRepository;

    public function __construct(IngredientRepository $ingredientRepository)
    {
        $this->ingredientRepository = $ingredientRepository;
    }

    public function getAllIngredients(): Collection
    {
        return $this->ingredientRepository->getAll();
    }

    public function getIngredientById($id): ?Ingredient
    {
        return $this->ingredientRepository->findById($id);
    }

    public function createIngredient(array $data): Ingredient
    {
        if (isset($data['img'])) {
            $data['img'] = $data['img']->store('ingredients', 'public');
        }
        return $this->ingredientRepository->create($data);
    }

    public function updateIngredient($id, array $data): bool
    {
        $ingredient = $this->ingredientRepository->findById($id);

        if (isset($data['img'])) {
            // Удаляем старое фото, если оно есть
            if ($ingredient->img) {
                Storage::disk('public')->delete($ingredient->img);
            }
            $data['img'] = $data['img']->store('ingredients', 'public');
        }
        return $this->ingredientRepository->update($id, $data);
    }

    public function deleteIngredient($id): bool|null
    {
        return $this->ingredientRepository->delete($id);
    }
}