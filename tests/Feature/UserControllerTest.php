<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use \App\Models\User;
use App\Services\UserService;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_show()
    {
        $requestedUser = User::factory()->create();

        // Проверка, если пользователь неаутентифицирован
        $response = $this->getJson("/api/user/{$requestedUser->id}");

        $response->assertUnauthorized();

        // Проверка, если пользователь аутентифицирован, но не владелец профиля
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->actingAs($user)->getJson("/api/user/{$requestedUser->id}");

        $response->assertForbidden();

        // Проверка, если пользователь аутентифицирован и владелец профиля
        $response = $this->actingAs($user)->getJson("/api/user/{$user->id}");

        $userService = new UserService($user);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'name',
                    'email',
                    'avatar',
                    'role',
                ]
            ])
            ->assertJsonFragment([
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->file,
                'role' => $userService->getRole(),
            ]);

        // Проверка, если пользователь аутентифицирован, не владелец профиля, но модератор
        $moderator = Sanctum::actingAs(User::factory()->state([
            'is_moderator' => true,
        ])->create());

        $response = $this->actingAs($moderator)->getJson("/api/user/{$user->id}");

        $response->assertForbidden();
    }
}
