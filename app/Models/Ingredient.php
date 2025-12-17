<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    //
    public function recipes()
    {
        return $this->belongsToMany(
            Recipe::class,
            'pivot_ingredient_recipe',
            'ingredient_id',
            'recipe_id'
        )->withPivot('quantity');
    }
}
