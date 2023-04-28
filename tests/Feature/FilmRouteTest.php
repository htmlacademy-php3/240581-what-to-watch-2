<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use \App\Models\User;

class FilmRouteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Проверка метода get роута '/api/films'
     *
     * @return void
     */
    public function test_get_films()
    {
        // Проверка, если пользователь неаутентифицирован
        $response = $this->getJson('/api/films');

        $response
            ->assertOk()
            ->assertJsonStructure([
               // 'data' => []
            ]);

        // Проверка, если пользователь аутентифицирован
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->actingAs($user)->getJson('/api/films');

        $response
            ->assertOk()
            ->assertJsonStructure([
               // 'data' => []
            ]);

        // Проверка, если пользователь аутентифицирован как модератор
        $user = Sanctum::actingAs(User::factory()->moderator()->create());

        $response = $this->actingAs($user)->getJson('/api/films');

        $response
            ->assertOk()
            ->assertJsonStructure([
               // 'data' => []
            ]);
    }

    /**
     * Проверка метода get роута '/api/films/{id}'
     *
     * @return void
     */
    public function test_get_film()
    {
        $filmsId = 1;

        // Проверка, если пользователь неаутентифицирован
        $response = $this->getJson("/api/films/{$filmsId}");

        $response
            ->assertOk()
            ->assertJsonStructure([
               // 'data' => []
            ]);

        // Проверка, если пользователь аутентифицирован
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->actingAs($user)->getJson("/api/films/{$filmsId}");

        $response
            ->assertOk()
            ->assertJsonStructure([
               // 'data' => []
            ]);

        // Проверка, если пользователь аутентифицирован как модератор
        $user = Sanctum::actingAs(User::factory()->moderator()->create());

        $response = $this->actingAs($user)->getJson("/api/films/{$filmsId}");

        $response
            ->assertOk()
            ->assertJsonStructure([
               // 'data' => []
            ]);
    }

    /**
     * Проверка метода post роута '/api/films/{id}'
     *
     * @return void
     */
    public function test_post_film()
    {
        // Проверка, если пользователь неаутентифицирован
        $response = $this->postJson("/api/films?imdbId=tt0111161");

        $response->assertUnauthorized();

        // Проверка, если пользователь аутентифицирован
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->actingAs($user)->postJson("/api/films?imdbId=tt0111161");

        $response->assertForbidden();

        // Проверка, если пользователь аутентифицирован как модератор
        $user = Sanctum::actingAs(User::factory()->moderator()->create());

        $response = $this->actingAs($user)->postJson("/api/films?imdbId=tt0111161");

        $response->assertCreated();
    }

    /**
     * Проверка метода patch роута '/api/films/{id}'
     *
     * @return void
     */
    public function test_update_film()
    {
        $filmId = 1;
        // Проверка, если пользователь неаутентифицирован
        $response = $this->patchJson("/api/films/{$filmId}");

        $response->assertUnauthorized();

        // Проверка, если пользователь аутентифицирован
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->actingAs($user)->patchJson("/api/films/{$filmId}");

        $response->assertForbidden();

        // Проверка, если пользователь аутентифицирован как модератор
        $user = Sanctum::actingAs(User::factory()->moderator()->create());

        $response = $this->actingAs($user)->patchJson("/api/films/{$filmId}");

        $response
            ->assertOk()
            ->assertJsonStructure([
               // 'data' => []
            ]);
    }
}
