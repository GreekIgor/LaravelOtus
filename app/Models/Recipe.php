<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    //
    
    public function author()
    {
        return $this->belongsTo(User::class);
    }

    public function ingredients()
    {
        return $this->belongsToMany(
            Ingredient::class,
            'pivot_ingredient_recipe',
            'recipe_id',
            'ingredient_id'
        )->withPivot('quantity');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
