<?php

namespace Tests\Unit;

use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Tag;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_recipe_has_author_relationship(): void
    {
        $user = User::factory()->create(['name' => 'Test Author']);
        $recipe = Recipe::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $recipe->author);
        $this->assertEquals($user->id, $recipe->author->id);
        $this->assertEquals('Test Author', $recipe->author->name);
    }

    public function test_recipe_has_ingredients_relationship(): void
    {
        $recipe = Recipe::factory()->create();
        $unit = Unit::factory()->create();
        $ingredient1 = Ingredient::factory()->create(['unit_id' => $unit->id]);
        $ingredient2 = Ingredient::factory()->create(['unit_id' => $unit->id]);

        $recipe->ingredients()->attach([
            $ingredient1->id => ['quantity' => 100],
            $ingredient2->id => ['quantity' => 200],
        ]);

        $this->assertCount(2, $recipe->ingredients);
        $this->assertEquals(100, $recipe->ingredients->first()->pivot->quantity);
    }

    public function test_recipe_has_tags_relationship(): void
    {
        $recipe = Recipe::factory()->create();
        $tag1 = Tag::create(['name' => 'Tag 1']);
        $tag2 = Tag::create(['name' => 'Tag 2']);

        $recipe->tags()->attach([$tag1->id, $tag2->id]);

        $this->assertCount(2, $recipe->tags);
        $this->assertTrue($recipe->tags->contains($tag1));
        $this->assertTrue($recipe->tags->contains($tag2));
    }

    public function test_recipe_fillable_fields(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create([
            'title' => 'Test Recipe',
            'time' => 30,
            'difficulty' => 'easy',
            'instructions' => 'Test instructions',
            'user_id' => $user->id,
        ]);

        $this->assertEquals('Test Recipe', $recipe->title);
        $this->assertEquals(30, $recipe->time);
        $this->assertEquals('easy', $recipe->difficulty);
        $this->assertEquals('Test instructions', $recipe->instructions);
        $this->assertEquals($user->id, $recipe->user_id);
    }
}

