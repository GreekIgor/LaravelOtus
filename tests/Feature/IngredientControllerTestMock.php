<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\IngredientService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

class IngredientControllerTestMock extends TestCase
{
    /**
     * A basic feature test example.
     */
   public function test_create_ingredient_mock(){
        $adminUser = User::factory()->make(['role' => 'admin']);
        $fakeImage = UploadedFile::fake()->image('test.jpg');
        $this->mock(IngredientService::class, function (MockInterface $mockInterface){
            $mockInterface->shouldReceive('createIngredient')->once();
        });

        $ingredient = [
            'name'=> 'test ingredient',
            'unit_id' => 1, 
            'img' => $fakeImage
        ];
        $response = $this->actingAs($adminUser)->post('admin/ingredients', $ingredient);

        $response->assertStatus(302);
    }

    public function test_can_delete_ingredient_via_mock(): void
    {

        $adminUser = User::factory()->make(['role' => 'admin']);
        $ingredientId = 99;

        $this->mock(IngredientService::class, function (MockInterface $mock) use ($ingredientId) {
            $mock->shouldReceive('deleteIngredient')
                ->once()
                ->with($ingredientId)
                ->andReturn(true); 
        });

        $response = $this->actingAs($adminUser)
            ->delete('/admin/ingredients/' . $ingredientId);

        $response->assertStatus(302); 
        $response->assertRedirect('/admin/ingredients'); 
    }


    #[Test()]
    #[TestDox('Администратор может обновить ингредиент с изображением через Mock')]
    public function test_can_update_ingredient_via_mock(): void
    {

        $adminUser = User::factory()->make(['role' => 'admin']);
        $ingredientId = 99;
        $unitId = 1;
        
        $fakeImage = UploadedFile::fake()->image('updated.jpg');
        
        $updateData = [
            'name' => 'Updated Ingredient',
            'unit_id' => $unitId,
            'img' => $fakeImage,
        ];

        $this->mock(IngredientService::class, function (MockInterface $mock) use ($ingredientId, $updateData) {
            $mock->shouldReceive('updateIngredient')
                ->once()
                ->with(
                    $ingredientId, 
                    Mockery::on(function ($args) use ($updateData) {
                        return $args['name'] === $updateData['name'] 
                            && $args['unit_id'] === $updateData['unit_id']
                            && $args['img'] instanceof UploadedFile; 
                    })
                )
                ->andReturn(true);
        });

        $response = $this->actingAs($adminUser)
            ->put('/admin/ingredients/' . $ingredientId, $updateData);

        $response->assertStatus(302);
        $response->assertRedirect('/admin/ingredients');
    }

    #[Test()]
    #[TestDox('Администратор может создать ингредиент через Mock')]
    public function test_can_create_ingredient_with_mock(): void
    {
        $adminUser = User::factory()->make(['role' => 'admin']);
        $fakeImage = UploadedFile::fake()->image('test.jpg');
        
        $ingredientData = [
            'name' => 'Test Ingredient',
            'unit_id' => 1,
            'img' => $fakeImage,
        ];

        $this->mock(IngredientService::class, function (MockInterface $mock) use ($ingredientData) {
            $mock->shouldReceive('createIngredient')
                ->once()
                ->with(Mockery::on(function ($args) use ($ingredientData) {
                    return $args['name'] === $ingredientData['name']
                        && $args['unit_id'] === $ingredientData['unit_id']
                        && $args['img'] instanceof UploadedFile;
                }))
                ->andReturn((object)['id' => 1, 'name' => 'Test Ingredient']);
        });

        $response = $this->actingAs($adminUser)
            ->post('/admin/ingredients', $ingredientData);

        $response->assertStatus(302);
        $response->assertRedirect('/admin/ingredients');
    }

    #[Test()]
    #[TestDox('Валидация при создании ингредиента через Mock')]
    public function test_is_validated_when_creating_ingredient_with_mock(): void
    {
        $adminUser = User::factory()->make(['role' => 'admin']);
        $fakeImage = UploadedFile::fake()->image('test.jpg');

        // IngredientService не должен вызываться при ошибках валидации
        $this->mock(IngredientService::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('createIngredient');
        });

        $response = $this->actingAs($adminUser)->post('/admin/ingredients', [
            'name' => '', // Пустое имя для проверки валидации
            'unit_id' => null,
            'img' => $fakeImage,
        ]);

        $response->assertSessionHasErrors(['name', 'unit_id']);
    }

    #[Test()]
    #[TestDox('Страница ингредиентов доступна администратору через Mock')]
    public function test_ingredient_index_page_accessible_by_admin_with_mock(): void
    {
        $adminUser = User::factory()->make(['role' => 'admin']);

        $this->mock(IngredientService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getAllIngredients')
                ->once()
                ->andReturn(collect([
                    (object)['id' => 1, 'name' => 'Mock Ingredient 1'],
                    (object)['id' => 2, 'name' => 'Mock Ingredient 2'],
                ]));
        });

        $response = $this->actingAs($adminUser)->get('/admin/ingredients');
        $response->assertStatus(200);
    }

    #[Test()]
    #[TestDox('Создание ингредиента через AJAX с Mock')]
    public function test_store_ingredient_returns_json_when_ajax_with_mock(): void
    {
        $adminUser = User::factory()->make(['role' => 'admin']);
        $fakeImage = UploadedFile::fake()->image('test.jpg');

        $ingredientData = [
            'name' => 'Test Ingredient',
            'unit_id' => 1,
            'img' => $fakeImage,
        ];

        $this->mock(IngredientService::class, function (MockInterface $mock) use ($ingredientData) {
            $mock->shouldReceive('createIngredient')
                ->once()
                ->with(Mockery::on(function ($args) use ($ingredientData) {
                    return $args['name'] === $ingredientData['name']
                        && $args['unit_id'] === $ingredientData['unit_id'];
                }))
                ->andReturn((object)['id' => 1, 'name' => 'Test Ingredient']);
        });

        $response = $this->actingAs($adminUser)
            ->postJson('/admin/ingredients', $ingredientData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Ингредиент создан!'
            ]);
    }

    #[Test()]
    #[TestDox('Обновление ингредиента через AJAX с Mock')]
    public function test_update_ingredient_returns_json_when_ajax_with_mock(): void
    {
        $adminUser = User::factory()->make(['role' => 'admin']);
        $ingredientId = 99;
        $fakeImage = UploadedFile::fake()->image('updated.jpg');

        $updateData = [
            'name' => 'Updated Ingredient',
            'unit_id' => 1,
            'img' => $fakeImage,
        ];

        $this->mock(IngredientService::class, function (MockInterface $mock) use ($ingredientId, $updateData) {
            $mock->shouldReceive('updateIngredient')
                ->once()
                ->with($ingredientId, Mockery::on(function ($args) use ($updateData) {
                    return $args['name'] === $updateData['name'];
                }))
                ->andReturn(true);
        });

        $response = $this->actingAs($adminUser)
            ->putJson('/admin/ingredients/' . $ingredientId, $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Ингредиент обновлен!'
            ]);
    }

    #[Test()]
    #[TestDox('Удаление ингредиента через AJAX с Mock')]
    public function test_destroy_ingredient_returns_json_when_ajax_with_mock(): void
    {
        $adminUser = User::factory()->make(['role' => 'admin']);
        $ingredientId = 99;

        $this->mock(IngredientService::class, function (MockInterface $mock) use ($ingredientId) {
            $mock->shouldReceive('deleteIngredient')
                ->once()
                ->with($ingredientId)
                ->andReturn(true);
        });

        $response = $this->actingAs($adminUser)
            ->deleteJson('/admin/ingredients/' . $ingredientId);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Ингредиент удален'
            ]);
    }

    #[Test()]
    #[TestDox('Валидация изображения отклоняет неверный формат через Mock')]
    public function test_image_validation_rejects_invalid_format_with_mock(): void
    {
        $adminUser = User::factory()->make(['role' => 'admin']);
        $invalidFile = UploadedFile::fake()->create('document.pdf', 100);

        $this->mock(IngredientService::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('createIngredient');
        });

        $response = $this->actingAs($adminUser)->post('/admin/ingredients', [
            'name' => 'Test Ingredient',
            'unit_id' => 1,
            'img' => $invalidFile,
        ]);

        $response->assertSessionHasErrors('img');
    }
}
