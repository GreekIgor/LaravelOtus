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

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
