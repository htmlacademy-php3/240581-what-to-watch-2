<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Models\Comment;
use App\Models\Film;
use App\Models\User;

class CommentRouteTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Проверка метода get роута "/api/comments/{filmId}"
     *
     * @return void
     */
    public function test_get_comments(): void
    {
        $film = Film::factory()->create();

        // Проверка, если пользователь неаутентифицирован
        $response = $this->getJson("/api/comments/{$film->id}");

        $response
            ->assertOk()
            ->assertJsonStructure([]);

        // Проверка, если пользователь аутентифицирован
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->actingAs($user)->getJson("/api/comments/{$film->id}");

        $response
            ->assertOk()
            ->assertJsonStructure([]);

        // Проверка, если пользователь аутентифицирован как модератор
        $user = Sanctum::actingAs(User::factory()->moderator()->create());

        $response = $this->actingAs($user)->getJson("/api/comments/{$film->id}");

        $response
            ->assertOk()
            ->assertJsonStructure([]);
    }

    /**
     * Проверка метода post роута "/api/comments/{filmId}"
     *
     * @return void
     */
    public function test_post_comments(): void
    {
        $film = Film::factory()->create();
        $reguestData = [
            'text' => $this->faker->paragraph(),
            'rating' => $this->faker->numberBetween(1, 10),
        ];

        // Проверка, если пользователь неаутентифицирован
        $response = $this->postJson("/api/comments/{$film->id}", $reguestData);

        $response->assertUnauthorized();

        // Проверка, если пользователь аутентифицирован
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->actingAs($user)->postJson("/api/comments/{$film->id}", $reguestData);

        $response->assertCreated();

        // Проверка, если пользователь аутентифицирован как модератор
        $user = Sanctum::actingAs(User::factory()->moderator()->create());

        $response = $this->actingAs($user)->postJson("/api/comments/{$film->id}", $reguestData);

        $response->assertCreated();
    }

    /**
     * Проверка метода patch роута '/api/comments/{comments}'
     *
     * @return void
     */
    public function test_patch_comments(): void
    {
        $film = Film::factory()->create();
        $unloggedUser = User::factory()->create();

        $unloggedUserComment = Comment::factory()
            ->state(new Sequence(
                fn ($sequence) => ['film_id' => $film, 'user_id' => $unloggedUser],
            ))
            ->create();

        $reguestData = [
            'text' => $this->faker->paragraph(),
            'rating' => $this->faker->numberBetween(1, 10),
        ];

        // Проверка, если пользователь неаутентифицирован
        $response = $this->patchJson("/api/comments/{$unloggedUserComment->id}", $reguestData);

        $response->assertUnauthorized();

        // Проверка, если пользователь аутентифицирован, но комментарий чужой
        $user = Sanctum::actingAs(User::factory()->create());

        $userComment = Comment::factory()
            ->state(new Sequence(
                fn ($sequence) => ['film_id' => $film, 'user_id' => $user],
            ))
            ->create();

        $response = $this->actingAs($user)->patchJson("/api/comments/{$unloggedUserComment->id}", $reguestData);

        $response->assertForbidden();

        // Проверка, если пользователь аутентифицирован и комментарий принадлежит ему
        $response = $this->actingAs($user)->patchJson("/api/comments/{$userComment->id}", $reguestData);

        $response
            ->assertStatus(Response::HTTP_ACCEPTED)
            ->assertJsonStructure([]);

        // Проверка, если пользователь аутентифицирован как модератор
        $moderator = Sanctum::actingAs(User::factory()->moderator()->create());
        $response = $this->actingAs($moderator)->patchJson("/api/comments/{$unloggedUserComment->id}", $reguestData);

        $response
            ->assertStatus(Response::HTTP_ACCEPTED)
            ->assertJsonStructure([]);
    }

    /**
     * Проверка метода delete роута '/api/comments/{comments}'
     *
     * @return void
     */
    public function test_delete_comments(): void
    {
        $film = Film::factory()->create();
        $unloggedUser = User::factory()->create();

        $unloggedUserComment = Comment::factory()
            ->state(new Sequence(
                fn ($sequence) => ['film_id' => $film, 'user_id' => $unloggedUser],
            ))
            ->create();

        // Проверка, если пользователь неаутентифицирован
        $response = $this->deleteJson("/api/comments/{$unloggedUserComment->id}");

        $response->assertUnauthorized();

        // Проверка, если пользователь аутентифицирован, но комментарий чужой
        $user = Sanctum::actingAs(User::factory()->create());

        $userComment = Comment::factory()
            ->state(new Sequence(
                fn ($sequence) => ['film_id' => $film, 'user_id' => $user],
            ))
            ->create();

        $response = $this->actingAs($user)->deleteJson("/api/comments/{$unloggedUserComment->id}");

        $response->assertForbidden();

        // Проверка, если пользователь аутентифицирован и комментарий принадлежит ему
        $response = $this->actingAs($user)->deleteJson("/api/comments/{$userComment->id}");

        $response->assertNoContent();

        // Проверка, если пользователь аутентифицирован как модератор
        $user = Sanctum::actingAs(User::factory()->moderator()->create());

        $response = $this->actingAs($user)->deleteJson("/api/comments/{$unloggedUserComment->id}");

        $response->assertNoContent();
    }
}
