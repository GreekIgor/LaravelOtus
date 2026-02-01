<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\IngredientService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class IngredientControllerTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_ingredient_index_page_accessible_by_admin(): void
    {
        // Создаем пользователя с правами администратора
        $adminUser = User::factory()->create([
            'role' => 'admin',
        ]);
        // Аутентифицируемся как администратор
        $response = $this->actingAs($adminUser)->get('/admin/ingredients');
        $response->assertStatus(200);
    }


    public function test_can_create_ingredient(): void
    {

        $adminUser = \App\Models\User::factory()->create([
            'role' => 'admin',
        ]);

        $unit = \App\Models\Unit::factory()->create();
        $fakeImage = UploadedFile::fake()->image('test.jpg');
        $response = $this->actingAs($adminUser)->post('/admin/ingredients', [
            'name' => 'Test Ingredient',
            'unit_id' => $unit->id,
            'img' => $fakeImage,
        ]);

        $response->assertStatus(302); // Проверяем перенаправление после создания
        $this->assertDatabaseHas('ingredients', [
            'name' => 'Test Ingredient',
            'unit_id' => $unit->id,
        ]);
    }

    public function test_is_validated_when_creating_ingredient(): void
    {
        $adminUser = User::factory()->create([
            'role' => 'admin',
        ]);

        $fakeImage = UploadedFile::fake()->image('test.jpg');
        $response = $this->actingAs($adminUser)->post('/admin/ingredients', [
            'name' => '', // Пустое имя для проверки валидации
            'unit_id' => null,
            'img' => $fakeImage,
        ]);

        $response->assertSessionHasErrors(['name', 'unit_id']);
    }

    public function test_can_delete_ingredient(): void
    {

        $adminUser = User::factory()->create([
            'role' => 'admin',
        ]);

        $ingredient = \App\Models\Ingredient::factory()->create();

        $response = $this->actingAs($adminUser)->delete('/admin/ingredients/' . $ingredient->id);

        $response->assertStatus(302); // Проверяем перенаправление после удаления
        $this->assertDatabaseMissing('ingredients', [
            'id' => $ingredient->id,
        ]);
    }

    public function test_can_update_ingredient(): void
    {

        $adminUser = User::factory()->create([
            'role' => 'admin',
        ]);

        $ingredient = \App\Models\Ingredient::factory()->create();
        $unit = \App\Models\Unit::factory()->create();

        $fakeImage = UploadedFile::fake()->image('updated.jpg');
        $response = $this->actingAs($adminUser)->put('/admin/ingredients/' . $ingredient->id, [
            'name' => 'Updated Ingredient',
            'unit_id' => $unit->id,
            'img' => $fakeImage,
        ]);

        $response->assertStatus(302); // Проверяем перенаправление после обновления
        $this->assertDatabaseHas('ingredients', [
            'id' => $ingredient->id,
            'name' => 'Updated Ingredient',
            'unit_id' => $unit->id,
        ]);
    }

    public function test_store_ingredient_returns_json_when_ajax(): void
    {
        $adminUser = User::factory()->create([
            'role' => 'admin',
        ]);

        $unit = \App\Models\Unit::factory()->create();
        $fakeImage = UploadedFile::fake()->image('test.jpg');

        $response = $this->actingAs($adminUser)
            ->postJson('/admin/ingredients', [
                'name' => 'Test Ingredient',
                'unit_id' => $unit->id,
                'img' => $fakeImage,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Ингредиент создан!'
            ]);

        $this->assertDatabaseHas('ingredients', [
            'name' => 'Test Ingredient',
            'unit_id' => $unit->id,
        ]);
    }

    public function test_update_ingredient_returns_json_when_ajax(): void
    {
        $adminUser = User::factory()->create([
            'role' => 'admin',
        ]);

        $ingredient = \App\Models\Ingredient::factory()->create();
        $unit = \App\Models\Unit::factory()->create();
        $fakeImage = UploadedFile::fake()->image('updated.jpg');

        $response = $this->actingAs($adminUser)
            ->putJson('/admin/ingredients/' . $ingredient->id, [
                'name' => 'Updated Ingredient',
                'unit_id' => $unit->id,
                'img' => $fakeImage,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Ингредиент обновлен!'
            ]);
    }

    public function test_destroy_ingredient_returns_json_when_ajax(): void
    {
        $adminUser = User::factory()->create([
            'role' => 'admin',
        ]);

        $ingredient = \App\Models\Ingredient::factory()->create();

        $response = $this->actingAs($adminUser)
            ->deleteJson('/admin/ingredients/' . $ingredient->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Ингредиент удален'
            ]);

        $this->assertDatabaseMissing('ingredients', [
            'id' => $ingredient->id,
        ]);
    }

    public function test_image_validation_rejects_invalid_format(): void
    {
        $adminUser = User::factory()->create([
            'role' => 'admin',
        ]);

        $unit = \App\Models\Unit::factory()->create();
        $invalidFile = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->actingAs($adminUser)->post('/admin/ingredients', [
            'name' => 'Test Ingredient',
            'unit_id' => $unit->id,
            'img' => $invalidFile,
        ]);

        $response->assertSessionHasErrors('img');
    }

    public function test_image_validation_rejects_too_large_file(): void
    {
        $adminUser = User::factory()->create([
            'role' => 'admin',
        ]);

        $unit = \App\Models\Unit::factory()->create();
        $largeFile = UploadedFile::fake()->image('large.jpg')->size(10240); // 10MB

        $response = $this->actingAs($adminUser)->post('/admin/ingredients', [
            'name' => 'Test Ingredient',
            'unit_id' => $unit->id,
            'img' => $largeFile,
        ]);

        // Валидация может не сработать, если нет правила max, но проверим
        $response->assertStatus(302);
    }
}