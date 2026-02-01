<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

class AdminDashboardTestMock extends TestCase
{
    #[Test()]
    #[TestDox('Админ-дашборд требует роль админа через Mock')]
    public function test_admin_dashboard_requires_admin_role_with_mock(): void
    {
        $viewer = User::factory()->make(['role' => 'viewer']);

        $this->mock('gate', function (MockInterface $mock) {
            $mock->shouldReceive('allows')->with('view-admin-dashboard')->andReturn(false);
        });

        $response = $this->actingAs($viewer)->get('/admin');

        $response->assertStatus(403);
    }

    #[Test()]
    #[TestDox('Админ-дашборд доступен админу через Mock')]
    public function test_admin_dashboard_accessible_by_admin_with_mock(): void
    {
        $admin = User::factory()->make(['role' => 'admin']);

        $this->mock('gate', function (MockInterface $mock) {
            $mock->shouldReceive('allows')->with('view-admin-dashboard')->andReturn(true);
        });

        $this->mock(User::class, function (MockInterface $mock) {
            $mock->shouldReceive('count')->andReturn(10);
        });

        $this->mock(Recipe::class, function (MockInterface $mock) {
            $mock->shouldReceive('count')->andReturn(5);
        });

        $this->mock(Ingredient::class, function (MockInterface $mock) {
            $mock->shouldReceive('count')->andReturn(8);
        });

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
    }

    #[Test()]
    #[TestDox('Админ-дашборд отображает статистику через Mock')]
    public function test_admin_dashboard_displays_statistics_with_mock(): void
    {
        $admin = User::factory()->make(['role' => 'admin']);

        $this->mock('gate', function (MockInterface $mock) {
            $mock->shouldReceive('allows')->with('view-admin-dashboard')->andReturn(true);
        });

        $userMock = Mockery::mock('alias:' . User::class);
        $userMock->shouldReceive('count')->andReturn(10);
        $userMock->shouldReceive('selectRaw')
            ->with('DATE(created_at) as date, COUNT(*) as count')
            ->andReturnSelf();
        $userMock->shouldReceive('where')
            ->with('created_at', '>=', Mockery::any())
            ->andReturnSelf();
        $userMock->shouldReceive('groupBy')
            ->with('date')
            ->andReturnSelf();
        $userMock->shouldReceive('orderBy')
            ->with('date')
            ->andReturnSelf();
        $userMock->shouldReceive('get')
            ->andReturn(collect([]));
        $userMock->shouldReceive('withCount')
            ->with('recipes')
            ->andReturnSelf();
        $userMock->shouldReceive('orderBy')
            ->with('recipes_count', 'desc')
            ->andReturnSelf();
        $userMock->shouldReceive('take')
            ->with(10)
            ->andReturnSelf();
        $userMock->shouldReceive('get')
            ->andReturn(collect([]));

        $recipeMock = Mockery::mock('alias:' . Recipe::class);
        $recipeMock->shouldReceive('count')->andReturn(5);

        $ingredientMock = Mockery::mock('alias:' . Ingredient::class);
        $ingredientMock->shouldReceive('count')->andReturn(8);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
    }

    #[Test()]
    #[TestDox('Админ-дашборд показывает правильное количество пользователей через Mock')]
    public function test_admin_dashboard_shows_correct_user_count_with_mock(): void
    {
        $admin = User::factory()->make(['role' => 'admin']);

        $this->mock('gate', function (MockInterface $mock) {
            $mock->shouldReceive('allows')->with('view-admin-dashboard')->andReturn(true);
        });

        $this->mock(User::class, function (MockInterface $mock) {
            $mock->shouldReceive('count')->andReturn(6);
            $mock->shouldReceive('selectRaw')->andReturnSelf();
            $mock->shouldReceive('where')->andReturnSelf();
            $mock->shouldReceive('groupBy')->andReturnSelf();
            $mock->shouldReceive('orderBy')->andReturnSelf();
            $mock->shouldReceive('get')->andReturn(collect([]));
            $mock->shouldReceive('withCount')->andReturnSelf();
            $mock->shouldReceive('take')->andReturnSelf();
        });

        $this->mock(Recipe::class, function (MockInterface $mock) {
            $mock->shouldReceive('count')->andReturn(0);
        });

        $this->mock(Ingredient::class, function (MockInterface $mock) {
            $mock->shouldReceive('count')->andReturn(0);
        });

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
    }

    #[Test()]
    #[TestDox('Админ-дашборд показывает данные графика регистраций через Mock')]
    public function test_admin_dashboard_shows_registration_chart_data_with_mock(): void
    {
        $admin = User::factory()->make(['role' => 'admin']);

        $this->mock('gate', function (MockInterface $mock) {
            $mock->shouldReceive('allows')->with('view-admin-dashboard')->andReturn(true);
        });

        $mockData = collect([
            (object)['date' => '2024-01-01', 'count' => 5],
            (object)['date' => '2024-01-02', 'count' => 3],
        ]);

        $this->mock(User::class, function (MockInterface $mock) use ($mockData) {
            $mock->shouldReceive('count')->andReturn(10);
            $mock->shouldReceive('selectRaw')
                ->with('DATE(created_at) as date, COUNT(*) as count')
                ->andReturnSelf();
            $mock->shouldReceive('where')
                ->with('created_at', '>=', Mockery::any())
                ->andReturnSelf();
            $mock->shouldReceive('groupBy')
                ->with('date')
                ->andReturnSelf();
            $mock->shouldReceive('orderBy')
                ->with('date')
                ->andReturnSelf();
            $mock->shouldReceive('get')
                ->andReturn($mockData);
            $mock->shouldReceive('withCount')->andReturnSelf();
            $mock->shouldReceive('orderBy')->andReturnSelf();
            $mock->shouldReceive('take')->andReturnSelf();
            $mock->shouldReceive('get')->andReturn(collect([]));
        });

        $this->mock(Recipe::class, function (MockInterface $mock) {
            $mock->shouldReceive('count')->andReturn(0);
        });

        $this->mock(Ingredient::class, function (MockInterface $mock) {
            $mock->shouldReceive('count')->andReturn(0);
        });

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
    }

    #[Test()]
    #[TestDox('Админ-дашборд показывает ТОП авторов через Mock')]
    public function test_admin_dashboard_shows_top_authors_with_mock(): void
    {
        $admin = User::factory()->make(['role' => 'admin']);

        $this->mock('gate', function (MockInterface $mock) {
            $mock->shouldReceive('allows')->with('view-admin-dashboard')->andReturn(true);
        });

        $topAuthors = collect([
            (object)['id' => 1, 'name' => 'Author 1', 'recipes_count' => 10],
            (object)['id' => 2, 'name' => 'Author 2', 'recipes_count' => 5],
        ]);

        $this->mock(User::class, function (MockInterface $mock) use ($topAuthors) {
            $mock->shouldReceive('count')->andReturn(10);
            $mock->shouldReceive('selectRaw')->andReturnSelf();
            $mock->shouldReceive('where')->andReturnSelf();
            $mock->shouldReceive('groupBy')->andReturnSelf();
            $mock->shouldReceive('orderBy')->andReturnSelf();
            $mock->shouldReceive('get')->andReturn(collect([]));
            $mock->shouldReceive('withCount')
                ->with('recipes')
                ->andReturnSelf();
            $mock->shouldReceive('orderBy')
                ->with('recipes_count', 'desc')
                ->andReturnSelf();
            $mock->shouldReceive('take')
                ->with(10)
                ->andReturnSelf();
            $mock->shouldReceive('get')
                ->andReturn($topAuthors);
        });

        $this->mock(Recipe::class, function (MockInterface $mock) {
            $mock->shouldReceive('count')->andReturn(0);
        });

        $this->mock(Ingredient::class, function (MockInterface $mock) {
            $mock->shouldReceive('count')->andReturn(0);
        });

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
    }
}

