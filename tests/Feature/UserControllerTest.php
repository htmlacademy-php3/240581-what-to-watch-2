<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use \App\Models\User;
use App\Services\UserService;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест action show() UserController`а.
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

    /**
     * Тест action update() UserController`а.
     *
     * @return void
     */
    public function test_update()
    {
        $requestedUser = User::factory()->create();

        // Проверка, если пользователь неаутентифицирован
        $response = $this->patchJson("/api/user/{$requestedUser->id}", [
            'name' => $requestedUser->name,
            'email' => $requestedUser->email,
        ]);

        $response->assertUnauthorized();

        // Проверка, если пользователь аутентифицирован, но не владелец профиля

        // Пользователь с исходными эталонными данными
        $user = Sanctum::actingAs(User::factory()->create([
            'name' => 'Abigail',
            'email' => 'email@email.com',
            'password' => '12345678',
        ]));

        // Логикой контроллера предусматривается, что производится обновление профиля пользователя, соответствующего текущему пользователю ($user = Auth::user();), независимо от того, какой id указан в uri. Фактически, пользователь будет редактировать свой профиль, поэтому при попытке указать email чужого профиля (обязательный параметр) будет ошибка валидации.
        $response = $this->patchJson("/api/user/{$requestedUser->id}", [
            'name' => $requestedUser->name,
            'email' => $requestedUser->email,
        ]);

        $response->assertInvalid('email');

        // Тоже самое в случае пользователя с ролью модератора.
        $moderator = Sanctum::actingAs(User::factory()->state([
            'is_moderator' => true,
        ])->create());

        $response = $this->actingAs($moderator)->patchJson("/api/user/{$user->id}", [
            'name' => $requestedUser->name,
            'email' => $requestedUser->email,
        ]);

        $response->assertInvalid('email');

        // Проверка, если пользователь аутентифицирован и владелец профиля
        Storage::fake('avatars');

        // а) Проверка работы при отсутствии новых данных
        $response = $this->actingAs($user)->patchJson("/api/user/{$user->id}", [
            'name' => $user->name,
            'email' => $user->email,
        ]);

        $response->assertStatus(Response::HTTP_ACCEPTED);

        // б) Проверка работы при изменении данных пользователя
        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($user)->patchJson("/api/user/
        {$user->id}", [
            'name' => 'NotAbigail',
            'email' => 'newemail@email.com',
            'password' => '87654321',
            'password_confirmation' => '87654321',
            'file' => $file,
        ]);

        $response->assertStatus(Response::HTTP_ACCEPTED);

        // Проверка данных пользователя на соответствие нововведённым
        $this->assertEquals('NotAbigail', $user->name);
        $this->assertEquals('newemail@email.com', $user->email);
        $this->assertEquals(true, Hash::check('87654321', $user->password));
        Storage::disk('avatars')->assertExists("avatars/{$file->hashName()}");
        $this->assertEquals("avatars/{$file->hashName()}", $user->file);

        // Проверка удаления старого аватара их хранилища
        $newFile = UploadedFile::fake()->image('newAvatar.jpg');

        $response = $this->actingAs($user)->patchJson("/api/user/
        {$user->id}", [
            'name' => 'NotAbigail',
            'email' => 'newemail@email.com',
            'file' => $newFile,
        ]);

        Storage::disk('avatars')->assertMissing("avatars/{$file->hashName()}");
    }
}
