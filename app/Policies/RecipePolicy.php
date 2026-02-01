<?php

namespace App\Policies;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;

class RecipePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Recipe $recipe): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Используем разрешение create-recipes вместо проверки ролей
        return Gate::forUser($user)->allows('create-recipes');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Recipe $recipe): bool
    {
        // Используем разрешение edit-own-recipes вместо проверки ролей
        return Gate::forUser($user)->allows('edit-own-recipes', $recipe);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Recipe $recipe): bool
    {
        // Используем разрешение delete-recipes вместо проверки роли
        return Gate::forUser($user)->allows('delete-recipes');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Recipe $recipe): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Recipe $recipe): bool
    {
        return false;
    }
}
