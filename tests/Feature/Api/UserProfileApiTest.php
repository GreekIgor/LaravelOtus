<?php

namespace Tests\Feature\Api;

use App\Models\ApiToken;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserProfileApiTest extends TestCase
{
    use RefreshDatabase;

    protected function createApiToken(User $user): string
    {
        $plainToken = Str::random(60);
        ApiToken::create([
            'user_id' => $user->id,
            'name' => 'test-token',
            'token' => hash('sha256', $plainToken),
            'abilities' => ['*'],
        ]);

        return $plainToken;
    }

    #[Test]
    public function test_get_profile_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/me');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'API token is missing.',
            ]);
    }

    #[Test]
    public function test_get_profile_fails_with_invalid_token(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer invalid-token-12345')
            ->getJson('/api/v1/me');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid API token.',
            ]);
    }

    #[Test]
    public function test_get_profile_returns_user_data_with_valid_token(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'moderator',
        ]);

        $token = $this->createApiToken($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'created_at',
                ],
            ])
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'role' => 'moderator',
                ],
            ]);
    }

    #[Test]
    public function test_get_profile_includes_recipes_count(): void
    {
        $user = User::factory()->create();
        
        // Создаем 3 рецепта для пользователя
        Recipe::factory()->count(3)->create(['user_id' => $user->id]);

        $token = $this->createApiToken($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'recipes_count',
                ],
            ])
            ->assertJson([
                'data' => [
                    'recipes_count' => 3,
                ],
            ]);
    }

    #[Test]
    public function test_get_profile_includes_recent_recipes(): void
    {
        $user = User::factory()->create();
        
        // Создаем 7 рецептов для пользователя
        Recipe::factory()->count(7)->create(['user_id' => $user->id]);

        $token = $this->createApiToken($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'recent_recipes' => [
                        '*' => [
                            'id',
                            'title',
                            'created_at',
                        ],
                    ],
                ],
            ]);

        $recentRecipes = $response->json('data.recent_recipes');
        $this->assertCount(5, $recentRecipes, 'Should return only 5 most recent recipes');
    }

    #[Test]
    public function test_get_profile_with_no_recipes(): void
    {
        $user = User::factory()->create();
        $token = $this->createApiToken($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/me');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'recipes_count' => 0,
                ],
            ]);

        // recent_recipes должен отсутствовать или быть пустым, если рецептов нет
        $data = $response->json('data');
        if (isset($data['recent_recipes'])) {
            $this->assertEmpty($data['recent_recipes']);
        }
    }

    #[Test]
    public function test_logout_requires_authentication(): void
    {
        $response = $this->postJson('/api/v1/auth/logout');

        $response->assertStatus(401);
    }

    #[Test]
    public function test_logout_with_valid_token(): void
    {
        $user = User::factory()->create();
        $token = $this->createApiToken($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logged out.',
            ]);

        // Проверяем, что токен удален
        $tokenHash = hash('sha256', $token);
        $this->assertDatabaseMissing('api_tokens', [
            'token' => $tokenHash,
        ]);
    }
}
