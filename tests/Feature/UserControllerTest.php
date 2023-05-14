<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use App\Services\UserService;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Тест action show() UserController`а.
     *
     * @return void
     */
    public function test_index(): void
    {
        User::factory()->create();

        // Проверка, если пользователь неаутентифицирован
        $response = $this->getJson('/api/user');

        $response->assertUnauthorized();

        // Проверка, если пользователь аутентифицирован
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->actingAs($user)->getJson('/api/user');

        $userService = new UserService($user);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'name',
                'email',
                'avatar',
                'role',
            ])
            ->assertJsonFragment([
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->file,
                'role' => $userService->getRole(),
            ]);
    }

    /**
     * Тест action update() UserController`а.
     *
     * @return void
     */
    public function test_update(): void
    {
        $requestedUser = User::factory()->create();

        // Проверка, если пользователь неаутентифицирован
        $response = $this->patchJson('/api/user/', [
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
        $response = $this->actingAs($user)->patchJson('/api/user/', [
            'name' => $requestedUser->name,
            'email' => $requestedUser->email,
        ]);

        $response->assertInvalid('email');

        // Проверка, если пользователь аутентифицирован и владелец профиля
        Storage::fake('avatars');

        // а) Проверка работы при отсутствии новых данных
        $response = $this->actingAs($user)->patchJson('/api/user/', [
            'name' => $user->name,
            'email' => $user->email,
        ]);

        $response->assertStatus(Response::HTTP_ACCEPTED);

        // б) Проверка работы при изменении данных пользователя
        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($user)->patchJson('/api/user/', [
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
        Storage::disk()->assertExists("avatars/{$file->hashName()}");
        $this->assertEquals("avatars/{$file->hashName()}", $user->file);

        // Проверка удаления старого аватара из хранилища
        $newFile = UploadedFile::fake()->image('newAvatar.jpg');

        $response = $this->actingAs($user)->patchJson('/api/user/', [
            'name' => 'NotAbigail',
            'email' => 'newemail@email.com',
            'file' => $newFile,
        ]);

        Storage::disk()->assertMissing("avatars/{$file->hashName()}");
    }
}
