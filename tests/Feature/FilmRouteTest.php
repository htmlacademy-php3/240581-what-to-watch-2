<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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
                'data' => []
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
                'data' => []
            ]);
    }

    /**
     * Проверка метода get роута '/api/films/{id}'
     *
     * @return void
     */
    public function test_post_film()
    {
        $filmsId = 1;
        // Проверка, если пользователь неаутентифицирован
        $response = $this->postJson("/api/films/{$filmsId}");

        $response->assertUnauthorized();
    }

    /**
     * Проверка метода get роута '/api/films/{id}'
     *
     * @return void
     */
    public function test_patch_film()
    {
        $filmId = 1;
        // Проверка, если пользователь неаутентифицирован
        $response = $this->patchJson("/api/films/{$filmId}");

        $response->assertUnauthorized();
    }
}
