<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class IngredientFactory extends Factory
{
    protected $model = \App\Models\Ingredient::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'quantity' => $this->faker->randomFloat(2, 0.1, 100),
            'unit_id' => \App\Models\Unit::factory(),
            'img' => $this->faker->imageUrl(),
        ];
    }
}
