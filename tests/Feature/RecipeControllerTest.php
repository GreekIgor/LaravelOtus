<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\IngredientService;
use App\Services\RecipeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

class RecipeControllerTest extends TestCase
{



    #[Test()]
    #[TestDox('Администратор видит страницу редактирования рецепта')]
    public function test_admin_see_edit_page_recipe()
    {
    $recipeID = 99;
    $recipeData = [];

     $admin = User::factory()->make(['role' => 'admin']);
     $this->mock(RecipeService::class, function(MockInterface $mock){
        $mock->shouldReceive('getRecipeById')
        ->once()
        ->with($recipeID)
        ->andReturn((object) array_merge(['id'=>$recipeID], $recipeData));

     });   

     $response = $this->actingAs($admin)
     ->get('/admin/recipes/'.$recipeID.'/edit');

     $response->assertStatus(200);



    }

    #[Test()]
    #[TestDox('Администратор может создать рецепт через мок-сервис')]
    public function test_admin_can_create_recipe_via_mocked_service(): void
    {

        $admin = User::factory()->make(['role' => 'admin']);


        $this->mock(RecipeService::class, function (MockInterface $mock) {

            $mock->shouldReceive('createRecipe')
                ->once()
                ->with(Mockery::subset(['title' => 'New Pasta']))
                ->andReturn((object)['id' => 1, 'title' => 'New Pasta']);
        });


        $response = $this->actingAs($admin)
            ->post('/admin/recipes', [
                'title' => 'New Pasta',
                'description' => 'Delicious description',
            ]);


        $response->assertStatus(302);
        $response->assertRedirect('/admin/recipes');
    }

    #[Test()]
    #[TestDox('Администратор может обновить рецепт')]
    public function test_admin_can_update_recipe_via_mocked_service(){

       $admin = User::factory()->make(['role' => 'admin']);

       $recipeID = 99;

       $recipeData = [
        'name'=> "test recipe",
        'descriptions' => 'тестовое описание'
       ]; 

       $this->mock(RecipeService::class, function (MockInterface $mock) use ($recipeID, $recipeData) {
            // Ожидаем, что контроллер вызовет метод updateRecipe один раз
            $mock->shouldReceive('updateRecipe')
                ->once()
                ->with($recipeID, Mockery::subset($recipeData)) // Проверяем, что данные дошли
                ->andReturn((object) array_merge(['id'=>$recipeID], $recipeData));
        });

        $response = $this->actingAs($admin)->put('/admin/recipes/'.$recipeID, $recipeData);
        $response->assertStatus(302);
    }

    #[Test()]
    #[TestDox('Администратор видит страницу рецептов')]
    public function test_index_page_displays_recipes_from_mock(): void
    {
        $admin = User::factory()->make(['role' => 'admin']);

        // Мокаем получение списка рецептов
        $this->mock(RecipeService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getAllRecipes')
                ->once()
                ->andReturn(collect([
                    (object)['id' => 1, 'title' => 'Mock Recipe 1', 'author'=> ['name'=> 'user']],
                    (object)['id' => 2, 'title' => 'Mock Recipe 2', 'author'=> ['name'=> 'user']],
                ]));
        });

        $response = $this->actingAs($admin)->get('/recipe-list');

        $response->assertStatus(200);
        // Проверяем, что данные из Мока попали на страницу
        $response->assertSee('Mock Recipe 1');
        $response->assertSee('Mock Recipe 2');
    }

    #[Test()]
    #[TestDox('Отображение детальной страницы рецепта через Mock')]
    public function test_show_recipe_with_mock(): void
    {
        $recipeID = 99;
        $recipeData = [
            'id' => $recipeID,
            'title' => 'Test Recipe',
            'instructions' => 'Test instructions',
        ];

        $this->mock(RecipeService::class, function (MockInterface $mock) use ($recipeID, $recipeData) {
            $mock->shouldReceive('getRecipeById')
                ->once()
                ->with($recipeID)
                ->andReturn((object) $recipeData);
        });

        $response = $this->get('/recipe/' . $recipeID);

        $response->assertStatus(200);
    }

    #[Test()]
    #[TestDox('Список рецептов отображается через Mock')]
    public function test_show_recipe_list_with_mock(): void
    {
        $this->mock(RecipeService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getAllRecipes')
                ->once()
                ->andReturn(collect([
                    (object)['id' => 1, 'title' => 'Recipe 1', 'author' => ['name' => 'user']],
                    (object)['id' => 2, 'title' => 'Recipe 2', 'author' => ['name' => 'user']],
                ]));
        });

        $response = $this->get('/recipe-list');

        $response->assertStatus(200);
    }

    #[Test()]
    #[TestDox('Форма создания рецепта доступна через Mock')]
    public function test_show_recipe_create_with_mock(): void
    {
        $admin = User::factory()->make(['role' => 'admin']);

        $this->mock(IngredientService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getAllIngredients')
                ->once()
                ->andReturn(collect([
                    (object)['id' => 1, 'name' => 'Ingredient 1'],
                    (object)['id' => 2, 'name' => 'Ingredient 2'],
                ]));
        });

        $response = $this->actingAs($admin)->get('/recipe-create');

        $response->assertStatus(200);
    }

    #[Test()]
    #[TestDox('Форма редактирования рецепта доступна через Mock')]
    public function test_show_recipe_edit_with_mock(): void
    {
        $admin = User::factory()->make(['role' => 'admin']);
        $recipeID = 99;

        $this->mock(RecipeService::class, function (MockInterface $mock) use ($recipeID) {
            $mock->shouldReceive('getRecipeById')
                ->once()
                ->with($recipeID)
                ->andReturn((object)['id' => $recipeID, 'title' => 'Test Recipe']);
        });

        $this->mock(IngredientService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getAllIngredients')
                ->once()
                ->andReturn(collect([]));
        });

        $response = $this->actingAs($admin)->get('/recipe-edit/' . $recipeID);

        $response->assertStatus(200);
    }

    #[Test()]
    #[TestDox('Удаление рецепта через Mock')]
    public function test_destroy_recipe_with_mock(): void
    {
        $admin = User::factory()->make(['role' => 'admin']);
        $recipeID = 99;

        $this->mock(RecipeService::class, function (MockInterface $mock) use ($recipeID) {
            $mock->shouldReceive('deleteRecipe')
                ->once()
                ->with($recipeID)
                ->andReturn(true);
        });

        $response = $this->actingAs($admin)->delete('/admin/recipes/' . $recipeID);

        $response->assertStatus(302);
        $response->assertRedirect('/admin/recipes');
    }

    #[Test()]
    #[TestDox('AJAX endpoint для DataTables через Mock')]
    public function test_index_ajax_with_mock(): void
    {
        $admin = User::factory()->make(['role' => 'admin']);

        $this->mock(RecipeService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getAllRecipes')
                ->once()
                ->andReturn(collect([
                    (object)['id' => 1, 'title' => 'Recipe 1', 'author' => ['name' => 'user']],
                ]));
        });

        $response = $this->actingAs($admin)
            ->get('/admin/recipes?ajax=1&draw=1&start=0&length=10');

        $response->assertStatus(200);
    }
}