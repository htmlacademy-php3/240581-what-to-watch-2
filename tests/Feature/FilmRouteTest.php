<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use \App\Models\Film;
use \App\Models\User;

class FilmRouteTest extends TestCase
{
    use RefreshDatabase, WithFaker;

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
        $film = Film::factory()->create();

        // Проверка, если пользователь неаутентифицирован
        $response = $this->getJson("/api/films/{$film->id}");

        $response
            ->assertOk()
            ->assertJsonStructure([
                // 'data' => []
            ]);

        // Проверка, если пользователь аутентифицирован
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->actingAs($user)->getJson("/api/films/{$film->id}");

        $response
            ->assertOk()
            ->assertJsonStructure([
                // 'data' => []
            ]);

        // Проверка, если пользователь аутентифицирован как модератор
        $user = Sanctum::actingAs(User::factory()->moderator()->create());

        $response = $this->actingAs($user)->getJson("/api/films/{$film->id}");

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
        $film = Film::factory()->create();

        $reguestData = ['name' => $this->faker->sentence(), 'imdb_id' => $film->imdb_id, 'status' => FILM::FILM_STATUS_MAP['ready']];

        // Проверка, если пользователь неаутентифицирован
        $response = $this->patchJson("/api/films/{$film->id}", $reguestData);

        $response->assertUnauthorized();

        // Проверка, если пользователь аутентифицирован
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->actingAs($user)->patchJson("/api/films/{$film->id}", $reguestData);

        $response->assertForbidden();

        // Проверка, если пользователь аутентифицирован как модератор
        $user = Sanctum::actingAs(User::factory()->moderator()->create());

        $response = $this->actingAs($user)->patchJson("/api/films/{$film->id}", $reguestData);

        $response
            ->assertStatus(Response::HTTP_ACCEPTED)
            ->assertJsonStructure([
                // 'data' => []
            ]);
    }
}
