<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Models\Genre;
use App\Models\User;

class GenreRouteTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

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
                //'data' => []
            ]);

        // Проверка, если пользователь аутентифицирован
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->actingAs($user)->getJson('/api/genres');

        $response
            ->assertOk()
            ->assertJsonStructure([
                // 'data' => []
            ]);

        // Проверка, если пользователь аутентифицирован как модератор
        $user = Sanctum::actingAs(User::factory()->moderator()->create());

        $response = $this->actingAs($user)->getJson('/api/genres');

        $response
            ->assertOk()
            ->assertJsonStructure([
                // 'data' => []
            ]);
    }

    /**
     * Проверка метода patch роута '/api/genres/{genres}'
     *
     * @return void
     */
    public function test_update_genres()
    {
        $genre = Genre::factory()->create();
        $genresId = $genre->id;
        $newGenreTitle = $this->faker->word();

        // Проверка, если пользователь неаутентифицирован
        $response = $this->patchJson("/api/genres/{$genresId}", [
            'title' => $newGenreTitle,
        ]);

        $response->assertUnauthorized();

        // Проверка, если пользователь аутентифицирован
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->actingAs($user)->patchJson("/api/genres/{$genresId}", [
            'title' => $newGenreTitle,
        ]);

        $response->assertForbidden();

        // Проверка, если пользователь аутентифицирован как модератор
        $user = Sanctum::actingAs(User::factory()->moderator()->create());

        $response = $this->actingAs($user)->patchJson("/api/genres/{$genresId}", [
            'title' => $newGenreTitle,
        ]);

        $response
            ->assertStatus(Response::HTTP_ACCEPTED)
            ->assertJsonStructure([
                //'data' => []
            ]);
    }
}
