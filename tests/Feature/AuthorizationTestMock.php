<?php

namespace Tests\Feature;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

class AuthorizationTestMock extends TestCase
{
    #[Test()]
    #[TestDox('RecipePolicy view разрешает всем через Mock')]
    public function test_recipe_policy_view_allows_everyone_with_mock(): void
    {
        $user = User::factory()->make(['id' => 1]);
        $recipe = Mockery::mock(Recipe::class);
        $recipe->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $this->assertTrue(Gate::forUser($user)->allows('view', $recipe));
    }

    #[Test()]
    #[TestDox('RecipePolicy create разрешает админу через Mock')]
    public function test_recipe_policy_create_allows_admin_with_mock(): void
    {
        $admin = User::factory()->make(['role' => 'admin']);

        $this->assertTrue(Gate::forUser($admin)->allows('create', Recipe::class));
    }

    #[Test()]
    #[TestDox('RecipePolicy create разрешает модератору через Mock')]
    public function test_recipe_policy_create_allows_moderator_with_mock(): void
    {
        $moderator = User::factory()->make(['role' => 'moderator']);

        $this->assertTrue(Gate::forUser($moderator)->allows('create', Recipe::class));
    }

    #[Test()]
    #[TestDox('RecipePolicy create запрещает viewer через Mock')]
    public function test_recipe_policy_create_denies_viewer_with_mock(): void
    {
        $viewer = User::factory()->make(['role' => 'viewer']);

        $this->assertFalse(Gate::forUser($viewer)->allows('create', Recipe::class));
    }

    #[Test()]
    #[TestDox('RecipePolicy update разрешает админу через Mock')]
    public function test_recipe_policy_update_allows_admin_with_mock(): void
    {
        $admin = User::factory()->make(['role' => 'admin', 'id' => 1]);
        $recipe = Mockery::mock(Recipe::class);
        $recipe->shouldReceive('getAttribute')->with('user_id')->andReturn(1);

        $this->assertTrue(Gate::forUser($admin)->allows('update', $recipe));
    }

    #[Test()]
    #[TestDox('RecipePolicy update разрешает модератору для своего рецепта через Mock')]
    public function test_recipe_policy_update_allows_moderator_for_own_recipe_with_mock(): void
    {
        $moderator = User::factory()->make(['role' => 'moderator', 'id' => 1]);
        $recipe = Mockery::mock(Recipe::class);
        $recipe->shouldReceive('getAttribute')->with('user_id')->andReturn(1);

        $this->assertTrue(Gate::forUser($moderator)->allows('update', $recipe));
    }

    #[Test()]
    #[TestDox('RecipePolicy update запрещает модератору для чужого рецепта через Mock')]
    public function test_recipe_policy_update_denies_moderator_for_other_recipe_with_mock(): void
    {
        $moderator = User::factory()->make(['role' => 'moderator', 'id' => 1]);
        $recipe = Mockery::mock(Recipe::class);
        $recipe->shouldReceive('getAttribute')->with('user_id')->andReturn(2);

        $this->assertFalse(Gate::forUser($moderator)->allows('update', $recipe));
    }

    #[Test()]
    #[TestDox('RecipePolicy delete разрешает админу через Mock')]
    public function test_recipe_policy_delete_allows_admin_with_mock(): void
    {
        $admin = User::factory()->make(['role' => 'admin']);
        $recipe = Mockery::mock(Recipe::class);

        $this->assertTrue(Gate::forUser($admin)->allows('delete', $recipe));
    }

    #[Test()]
    #[TestDox('RecipePolicy delete запрещает модератору через Mock')]
    public function test_recipe_policy_delete_denies_moderator_with_mock(): void
    {
        $moderator = User::factory()->make(['role' => 'moderator']);
        $recipe = Mockery::mock(Recipe::class);

        $this->assertFalse(Gate::forUser($moderator)->allows('delete', $recipe));
    }

    #[Test()]
    #[TestDox('Неавторизованный пользователь не может получить доступ к админ-маршрутам через Mock')]
    public function test_unauthorized_user_cannot_access_admin_routes_with_mock(): void
    {
        $this->mock('auth', function (MockInterface $mock) {
            $mock->shouldReceive('check')->andReturn(false);
        });

        $response = $this->get('/admin/recipes');

        $response->assertRedirect('/login');
    }

    #[Test()]
    #[TestDox('Viewer не может получить доступ к админ-маршрутам через Mock')]
    public function test_viewer_cannot_access_admin_routes_with_mock(): void
    {
        $viewer = User::factory()->make(['role' => 'viewer']);

        $this->mock(Gate::class, function (MockInterface $mock) {
            $mock->shouldReceive('allows')->with('view-admin-dashboard')->andReturn(false);
        });

        $response = $this->actingAs($viewer)->get('/admin/recipes');

        $response->assertStatus(403);
    }

    #[Test()]
    #[TestDox('Админ может получить доступ к админ-маршрутам через Mock')]
    public function test_admin_can_access_admin_routes_with_mock(): void
    {
        $admin = User::factory()->make(['role' => 'admin']);

        $this->mock(Gate::class, function (MockInterface $mock) {
            $mock->shouldReceive('allows')->with('view-admin-dashboard')->andReturn(true);
        });

        $response = $this->actingAs($admin)->get('/admin/recipes');

        $response->assertStatus(200);
    }

    #[Test()]
    #[TestDox('Проверка разрешения create-recipes для админа')]
    public function test_admin_can_create_recipes_with_mock(): void
    {
        $admin = User::factory()->make(['role' => 'admin']);

        $this->assertTrue(Gate::forUser($admin)->allows('create-recipes'));
    }

    #[Test()]
    #[TestDox('Проверка разрешения create-recipes для модератора')]
    public function test_moderator_can_create_recipes_with_mock(): void
    {
        $moderator = User::factory()->make(['role' => 'moderator']);

        $this->assertTrue(Gate::forUser($moderator)->allows('create-recipes'));
    }

    #[Test()]
    #[TestDox('Проверка разрешения manage-ingredients только для админа')]
    public function test_only_admin_can_manage_ingredients_with_mock(): void
    {
        $admin = User::factory()->make(['role' => 'admin']);
        $moderator = User::factory()->make(['role' => 'moderator']);

        $this->assertTrue(Gate::forUser($admin)->allows('manage-ingredients'));
        $this->assertFalse(Gate::forUser($moderator)->allows('manage-ingredients'));
    }
}

