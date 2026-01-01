<?php
namespace App\Repositories;

use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Collection;

class IngredientRepository
{
    /**
     * Get all ingredients.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(): Collection
    {
        return Ingredient::all();
    }

    /**
     * Find an ingredient by its ID.
     *
     * @param  int  $id
     * @return Ingredient|null
     */
    public function findById($id): ?Ingredient
    {
        return Ingredient::find($id);
    }

    /**
     * Create a new ingredient.
     *
     * @param  array  $data
     * @return Ingredient
     */
    public function create(array $data): Ingredient
    {
        return Ingredient::create($data);
    }

    /**
     * Update an existing ingredient.
     *
     * @param  int  $id
     * @param  array  $data
     * @return bool
     */
    public function update($id, array $data): bool
    {
        $ingredient = $this->findById($id);
        if ($ingredient) {
            return $ingredient->update($data);
        }
        return false;
    }

    /**
     * Delete an ingredient by its ID.
     *
     * @param  int  $id
     * @return bool|null
     */
    public function delete($id): bool|null
    {
        $ingredient = $this->findById($id);
        if ($ingredient) {
            return $ingredient->delete();
        }
        return false;
    }
}