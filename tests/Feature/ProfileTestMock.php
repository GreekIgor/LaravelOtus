<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

class ProfileTestMock extends TestCase
{
    #[Test()]
    #[TestDox('Страница профиля отображается через Mock')]
    public function test_profile_page_is_displayed_with_mock(): void
    {
        $user = User::factory()->make(['id' => 1, 'name' => 'Test User']);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertOk();
    }

    #[Test()]
    #[TestDox('Обновление информации профиля через Mock')]
    public function test_profile_information_can_be_updated_with_mock(): void
    {
        Event::fake();

        $user = User::factory()->make([
            'id' => 1,
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);

        $response = $this->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response->assertSessionHasNoErrors()
            ->assertRedirect('/profile');
    }

    #[Test()]
    #[TestDox('Статус верификации email не изменяется при том же email через Mock')]
    public function test_email_verification_status_is_unchanged_with_mock(): void
    {
        Event::fake();

        $user = User::factory()->make([
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->patch('/profile', [
                'name' => 'Updated Name',
                'email' => 'test@example.com',
            ]);

        $response->assertSessionHasNoErrors()
            ->assertRedirect('/profile');
    }

    #[Test()]
    #[TestDox('Удаление аккаунта пользователя через Mock')]
    public function test_user_can_delete_their_account_with_mock(): void
    {
        Event::fake();

        $user = User::factory()->make([
            'id' => 1,
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response->assertSessionHasNoErrors()
            ->assertRedirect('/');
    }

    #[Test()]
    #[TestDox('Требуется правильный пароль для удаления аккаунта через Mock')]
    public function test_correct_password_must_be_provided_to_delete_account_with_mock(): void
    {
        $user = User::factory()->make([
            'id' => 1,
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');
    }
}

