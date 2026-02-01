<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ingredient extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'name',
        'unit_id',
        'img'
    ];
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
