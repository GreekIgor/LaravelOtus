<?php

namespace Tests\Unit;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_is_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isModerator());
        $this->assertFalse($admin->isViewer());
    }

    public function test_user_is_moderator(): void
    {
        $moderator = User::factory()->create(['role' => 'moderator']);

        $this->assertFalse($moderator->isAdmin());
        $this->assertTrue($moderator->isModerator());
        $this->assertFalse($moderator->isViewer());
    }

    public function test_user_is_viewer(): void
    {
        $viewer = User::factory()->create(['role' => 'viewer']);

        $this->assertFalse($viewer->isAdmin());
        $this->assertFalse($viewer->isModerator());
        $this->assertTrue($viewer->isViewer());
    }

    public function test_user_has_recipes_relationship(): void
    {
        $user = User::factory()->create();
        $recipe1 = Recipe::factory()->create(['user_id' => $user->id]);
        $recipe2 = Recipe::factory()->create(['user_id' => $user->id]);

        $this->assertCount(2, $user->recipes);
        $this->assertTrue($user->recipes->contains($recipe1));
        $this->assertTrue($user->recipes->contains($recipe2));
    }

    public function test_user_fillable_fields(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
    }
}

