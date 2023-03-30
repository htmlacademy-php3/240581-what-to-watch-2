<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use \App\Models\Comment;
use \App\Models\Film;
use \App\Models\User;

class CommentRouteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Проверка метода get роута '/api/films/{id}/comments'
     *
     * @return void
     */
    public function test_get_comments()
    {
        // Проверка, если пользователь неаутентифицирован
        $response = $this->getJson("/api/films/{id}/comments");

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => []
            ]);

        // Проверка, если пользователь аутентифицирован
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->actingAs($user)->getJson("/api/films/{id}/comments");

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => []
            ]);

        // Проверка, если пользователь аутентифицирован как модератор
        $user = Sanctum::actingAs(User::factory()->moderator()->create());

        $response = $this->actingAs($user)->getJson("/api/films/{id}/comments");

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => []
            ]);
    }

    /**
     * Проверка метода post роута '/api/films/{id}/comments'
     *
     * @return void
     */
    public function test_post_comments()
    {
        // Проверка, если пользователь неаутентифицирован
        $response = $this->postJson("/api/films/{id}/comments");

        $response->assertUnauthorized();

        // Проверка, если пользователь аутентифицирован
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->actingAs($user)->postJson("/api/films/{id}/comments");

        $response->assertCreated();

        // Проверка, если пользователь аутентифицирован как модератор
        $user = Sanctum::actingAs(User::factory()->moderator()->create());

        $response = $this->actingAs($user)->postJson("/api/films/{id}/comments");

        $response->assertCreated();
    }

    /**
     * Проверка метода patch роута '/api/comments/{comments}'
     *
     * @return void
     */
    public function test_patch_comments()
    {
        $film = Film::factory()->create();
        $unloggedUser = User::factory()->create();

        $unloggedUserComment = Comment::factory()
            ->state(new Sequence(
                fn ($sequence) => ['film_id' => $film, 'user_id' => $unloggedUser],
            ))
            ->create();

        // Проверка, если пользователь неаутентифицирован
        $response = $this->patchJson("/api/comments/{$unloggedUserComment->id}");

        $response->assertUnauthorized();

        // Проверка, если пользователь аутентифицирован, но комментарий чужой
        $user = Sanctum::actingAs(User::factory()->create());

        $userComment = Comment::factory()
            ->state(new Sequence(
                fn ($sequence) => ['film_id' => $film, 'user_id' => $user],
            ))
            ->create();

        $response = $this->actingAs($user)->patchJson("/api/comments/{$unloggedUserComment->id}");

        $response->assertForbidden();

        // Проверка, если пользователь аутентифицирован и комментарий принадлежит ему
        $response = $this->actingAs($user)->patchJson("/api/comments/{$userComment->id}");

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => []
            ]);

        // Проверка, если пользователь аутентифицирован как модератор
        $moderator = Sanctum::actingAs(User::factory()->moderator()->create());
        $response = $this->actingAs($moderator)->patchJson("/api/comments/{$unloggedUserComment->id}");

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => []
            ]);
    }

    /**
     * Проверка метода delete роута '/api/comments/{comments}'
     *
     * @return void
     */
    public function test_delete_comments()
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
