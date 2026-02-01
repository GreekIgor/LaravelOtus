<?php

namespace Tests\Unit;

use App\Models\Recipe;
use App\Repositories\RecipeRepository;
use App\Services\RecipeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class RecipeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_recipes_calls_repository(): void
    {
        $mockRepository = Mockery::mock(RecipeRepository::class);
        $expectedRecipes = collect([
            (object)['id' => 1, 'title' => 'Recipe 1'],
            (object)['id' => 2, 'title' => 'Recipe 2'],
        ]);

        $mockRepository->shouldReceive('getAllRecipes')
            ->once()
            ->andReturn($expectedRecipes);

        $service = new RecipeService($mockRepository);
        $result = $service->getAllRecipes();

        $this->assertEquals($expectedRecipes, $result);
    }

    public function test_get_recipe_by_id_calls_repository(): void
    {
        $mockRepository = Mockery::mock(RecipeRepository::class);
        $recipeId = 99;
        $expectedRecipe = (object)['id' => $recipeId, 'title' => 'Test Recipe'];

        $mockRepository->shouldReceive('findById')
            ->once()
            ->with($recipeId)
            ->andReturn($expectedRecipe);

        $service = new RecipeService($mockRepository);
        $result = $service->getRecipeById($recipeId);

        $this->assertEquals($expectedRecipe, $result);
    }

    public function test_create_recipe_processes_image(): void
    {
        Storage::fake('public');
        Auth::shouldReceive('id')->andReturn(1);

        $mockRepository = Mockery::mock(RecipeRepository::class);
        $fakeImage = UploadedFile::fake()->image('recipe.jpg');
        
        $recipeData = [
            'title' => 'Test Recipe',
            'image' => $fakeImage,
            'user_id' => 1,
        ];

        $expectedRecipe = new Recipe();
        $expectedRecipe->id = 1;
        $expectedRecipe->title = 'Test Recipe';

        $mockRepository->shouldReceive('create')
            ->once()
            ->with(
                Mockery::on(function ($data) {
                    return isset($data['image_path']) && $data['title'] === 'Test Recipe';
                }),
                []
            )
            ->andReturn($expectedRecipe);

        $service = new RecipeService($mockRepository);
        $result = $service->createRecipe($recipeData);

        $this->assertInstanceOf(Recipe::class, $result);
    }

    public function test_create_recipe_syncs_ingredients(): void
    {
        Auth::shouldReceive('id')->andReturn(1);

        $mockRepository = Mockery::mock(RecipeRepository::class);
        
        $recipeData = [
            'title' => 'Test Recipe',
            'ingredients' => [1, 2],
            'amounts' => [100, 200],
            'user_id' => 1,
        ];

        $expectedSyncData = [
            1 => ['quantity' => 100],
            2 => ['quantity' => 200],
        ];

        $expectedRecipe = new Recipe();
        $expectedRecipe->id = 1;

        $mockRepository->shouldReceive('create')
            ->once()
            ->with(
                Mockery::on(function ($data) {
                    return $data['title'] === 'Test Recipe';
                }),
                Mockery::on(function ($syncData) use ($expectedSyncData) {
                    return $syncData == $expectedSyncData;
                })
            )
            ->andReturn($expectedRecipe);

        $service = new RecipeService($mockRepository);
        $result = $service->createRecipe($recipeData);

        $this->assertInstanceOf(Recipe::class, $result);
    }

    public function test_update_recipe_calls_repository(): void
    {
        $mockRepository = Mockery::mock(RecipeRepository::class);
        $recipeId = 99;
        $recipe = new Recipe();
        $recipe->id = $recipeId;

        $updateData = [
            'title' => 'Updated Recipe',
        ];

        $syncData = [
            1 => ['quantity' => 100],
        ];

        $mockRepository->shouldReceive('findById')
            ->once()
            ->with($recipeId)
            ->andReturn($recipe);

        $updatedRecipe = new Recipe();
        $updatedRecipe->id = $recipeId;
        $updatedRecipe->title = 'Updated Recipe';

        $mockRepository->shouldReceive('update')
            ->once()
            ->with($recipe, $updateData, $syncData)
            ->andReturn($updatedRecipe);

        $service = new RecipeService($mockRepository);
        $result = $service->updateRecipe($recipeId, array_merge($updateData, [
            'ingredients' => [1],
            'amounts' => [100],
        ]));

        $this->assertInstanceOf(Recipe::class, $result);
    }

    public function test_delete_recipe_calls_repository(): void
    {
        $mockRepository = Mockery::mock(RecipeRepository::class);
        $recipeId = 99;

        $mockRepository->shouldReceive('delete')
            ->once()
            ->with($recipeId)
            ->andReturn(true);

        $service = new RecipeService($mockRepository);
        $result = $service->deleteRecipe($recipeId);

        $this->assertTrue($result);
    }
}

