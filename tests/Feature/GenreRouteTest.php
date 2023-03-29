<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreRouteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Проверка метода get роута '/api/genres'
     *
     * @return void
     */
    public function test_get_genres()
    {
        // Проверка, если пользователь неаутентифицирован
        $response = $this->getJson('/api/genres');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => []
            ]);
    }

    /**
     * Проверка метода patch роута '/api/genres/{genres}'
     *
     * @return void
     */
    public function test_update_genres()
    {
        $genresId = 1;

        // Проверка, если пользователь неаутентифицирован
        $response = $this->patchJson("/api/genres/{$genresId}");

        $response->assertUnauthorized();
    }
}
