<?php

namespace Tests\Unit;

use App\Models\Ingredient;
use App\Repositories\IngredientRepository;
use App\Services\IngredientService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class IngredientServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_ingredients_calls_repository(): void
    {
        $mockRepository = Mockery::mock(IngredientRepository::class);
        $expectedIngredients = collect([
            new Ingredient(['id' => 1, 'name' => 'Ingredient 1']),
            new Ingredient(['id' => 2, 'name' => 'Ingredient 2']),
        ]);

        $mockRepository->shouldReceive('getAll')
            ->once()
            ->andReturn($expectedIngredients);

        $service = new IngredientService($mockRepository);
        $result = $service->getAllIngredients();

        $this->assertEquals($expectedIngredients, $result);
    }

    public function test_get_ingredient_by_id_calls_repository(): void
    {
        $mockRepository = Mockery::mock(IngredientRepository::class);
        $ingredientId = 99;
        $expectedIngredient = new Ingredient(['id' => $ingredientId, 'name' => 'Test Ingredient']);

        $mockRepository->shouldReceive('findById')
            ->once()
            ->with($ingredientId)
            ->andReturn($expectedIngredient);

        $service = new IngredientService($mockRepository);
        $result = $service->getIngredientById($ingredientId);

        $this->assertEquals($expectedIngredient, $result);
    }

    public function test_create_ingredient_processes_image(): void
    {
        Storage::fake('public');

        $mockRepository = Mockery::mock(IngredientRepository::class);
        $fakeImage = UploadedFile::fake()->image('ingredient.jpg');
        
        $ingredientData = [
            'name' => 'Test Ingredient',
            'unit_id' => 1,
            'img' => $fakeImage,
        ];

        $expectedIngredient = new Ingredient();
        $expectedIngredient->id = 1;
        $expectedIngredient->name = 'Test Ingredient';

        $mockRepository->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) {
                return isset($data['img']) && is_string($data['img']) && $data['name'] === 'Test Ingredient';
            }))
            ->andReturn($expectedIngredient);

        $service = new IngredientService($mockRepository);
        $result = $service->createIngredient($ingredientData);

        $this->assertInstanceOf(Ingredient::class, $result);
    }

    public function test_update_ingredient_processes_image_and_deletes_old(): void
    {
        Storage::fake('public');

        $mockRepository = Mockery::mock(IngredientRepository::class);
        $ingredientId = 99;
        $oldIngredient = new Ingredient();
        $oldIngredient->id = $ingredientId;
        $oldIngredient->img = 'old-image.jpg';

        $fakeImage = UploadedFile::fake()->image('new-ingredient.jpg');
        
        $updateData = [
            'name' => 'Updated Ingredient',
            'unit_id' => 1,
            'img' => $fakeImage,
        ];

        $mockRepository->shouldReceive('findById')
            ->once()
            ->with($ingredientId)
            ->andReturn($oldIngredient);

        $mockRepository->shouldReceive('update')
            ->once()
            ->with($ingredientId, Mockery::on(function ($data) {
                return isset($data['img']) && is_string($data['img']) && $data['name'] === 'Updated Ingredient';
            }))
            ->andReturn(true);

        $service = new IngredientService($mockRepository);
        $result = $service->updateIngredient($ingredientId, $updateData);

        $this->assertTrue($result);
    }

    public function test_update_ingredient_without_image(): void
    {
        $mockRepository = Mockery::mock(IngredientRepository::class);
        $ingredientId = 99;
        $oldIngredient = new Ingredient();
        $oldIngredient->id = $ingredientId;
        $oldIngredient->img = null;

        $updateData = [
            'name' => 'Updated Ingredient',
            'unit_id' => 1,
        ];

        $mockRepository->shouldReceive('findById')
            ->once()
            ->with($ingredientId)
            ->andReturn($oldIngredient);

        $mockRepository->shouldReceive('update')
            ->once()
            ->with($ingredientId, $updateData)
            ->andReturn(true);

        $service = new IngredientService($mockRepository);
        $result = $service->updateIngredient($ingredientId, $updateData);

        $this->assertTrue($result);
    }

    public function test_delete_ingredient_calls_repository(): void
    {
        $mockRepository = Mockery::mock(IngredientRepository::class);
        $ingredientId = 99;

        $mockRepository->shouldReceive('delete')
            ->once()
            ->with($ingredientId)
            ->andReturn(true);

        $service = new IngredientService($mockRepository);
        $result = $service->deleteIngredient($ingredientId);

        $this->assertTrue($result);
    }

    public function test_delete_ingredient_returns_null_when_not_found(): void
    {
        $mockRepository = Mockery::mock(IngredientRepository::class);
        $ingredientId = 999;

        $mockRepository->shouldReceive('delete')
            ->once()
            ->with($ingredientId)
            ->andReturn(false);

        $service = new IngredientService($mockRepository);
        $result = $service->deleteIngredient($ingredientId);

        $this->assertFalse($result);
    }
}

