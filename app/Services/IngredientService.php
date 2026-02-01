<?php

namespace App\Services;
use App\Models\Ingredient;
use App\Repositories\IngredientRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
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
        return Cache::remember('ingredients.all', 900, function () {
            return $this->ingredientRepository->getAll();
        });
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
        $ingredient = $this->ingredientRepository->create($data);
        
        // Очистка кэша после создания ингредиента
        $this->clearIngredientCache();
        
        return $ingredient;
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
        $result = $this->ingredientRepository->update($id, $data);
        
        // Очистка кэша после обновления ингредиента
        $this->clearIngredientCache();
        
        return $result;
    }

    public function deleteIngredient($id): bool|null
    {
        $result = $this->ingredientRepository->delete($id);
        
        // Очистка кэша после удаления ингредиента
        $this->clearIngredientCache();
        
        return $result;
    }
    
    /**
     * Очистка кэша ингредиентов
     */
    protected function clearIngredientCache(): void
    {
        Cache::forget('ingredients.all');
        // Также очищаем статистику админ-панели, так как количество ингредиентов изменилось
        Cache::forget('admin.stats');
    }
}