<?php

namespace Tests\Unit;

use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IngredientModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_ingredient_has_unit_relationship(): void
    {
        $unit = Unit::factory()->create(['name' => 'кг']);
        $ingredient = Ingredient::factory()->create(['unit_id' => $unit->id]);

        $this->assertInstanceOf(Unit::class, $ingredient->unit);
        $this->assertEquals($unit->id, $ingredient->unit->id);
        $this->assertEquals('кг', $ingredient->unit->name);
    }

    public function test_ingredient_has_recipes_relationship(): void
    {
        $user = User::factory()->create();
        $ingredient = Ingredient::factory()->create();
        $recipe1 = Recipe::factory()->create(['user_id' => $user->id]);
        $recipe2 = Recipe::factory()->create(['user_id' => $user->id]);

        $ingredient->recipes()->attach([
            $recipe1->id => ['quantity' => 100],
            $recipe2->id => ['quantity' => 200],
        ]);

        $this->assertCount(2, $ingredient->recipes);
        $this->assertTrue($ingredient->recipes->contains($recipe1));
        $this->assertTrue($ingredient->recipes->contains($recipe2));
    }

    public function test_ingredient_fillable_fields(): void
    {
        $unit = Unit::factory()->create();
        $ingredient = Ingredient::factory()->create([
            'name' => 'Test Ingredient',
            'unit_id' => $unit->id,
            'img' => 'test-image.jpg',
        ]);

        $this->assertEquals('Test Ingredient', $ingredient->name);
        $this->assertEquals($unit->id, $ingredient->unit_id);
        $this->assertEquals('test-image.jpg', $ingredient->img);
    }
}

