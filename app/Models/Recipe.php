<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    //

    protected $fillable = ['title', 'time', 'difficulty', 'image_path', 'ingredients', 'amounts', 'units', 'instructions', 'user_id'];
    
    /**
     * Получить маршрутный ключ для модели.
     * Это гарантирует, что route model binding будет использовать ID.
     */
    public function getRouteKeyName()
    {
        return 'id';
    }
    
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
