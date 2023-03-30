<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use \App\Models\User;

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

        // Проверка, если пользователь аутентифицирован
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->actingAs($user)->getJson('/api/genres');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => []
            ]);

            // Проверка, если пользователь аутентифицирован как модератор
        $user = Sanctum::actingAs(User::factory()->moderator()->create());

        $response = $this->actingAs($user)->getJson('/api/genres');

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

        // Проверка, если пользователь аутентифицирован
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->actingAs($user)->patchJson("/api/genres/{$genresId}");

        $response->assertForbidden();

        // Проверка, если пользователь аутентифицирован как модератор
        $user = Sanctum::actingAs(User::factory()->moderator()->create());

        $response = $this->actingAs($user)->patchJson("/api/genres/{$genresId}");

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => []
            ]);
    }
}
