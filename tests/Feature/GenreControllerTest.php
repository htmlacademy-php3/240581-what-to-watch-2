<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use App\Models\Genre;
use Laravel\Sanctum\Sanctum;
use App\Models\User;

class GenreControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Проверка метода index() GenreController`а
     *
     * @return void
     */
    public function test_index()
    {
        $count = 10;
        Genre::factory($count)->create();

        $response = $this->getJson('/api/genres');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'created_at',
                        'updated_at'
                    ]
                ],
            ])
            // Проверка, что возвращено $count жанров
            ->assertJsonCount($count, 'data.*');
    }

    /**
     * Проверка метода update() GenreController`а
     *
     * @return void
     */
    public function test_update()
    {
        // Эталонные названия жанров
        $genreTitle = $this->faker->unique()->word();
        $newGenreTitle = $this->faker->unique()->word();
        $existingGenreTitle = $this->faker->unique()->word();

        // Эталоный и контрольный (существующие) жанры
        $requestedGenre = Genre::factory()->create([
            'title' => $genreTitle,
        ]);

        Genre::factory()->create([
            'title' => $existingGenreTitle,
        ]);

        // Проверка, если пользователь неаутентифицирован
        $response = $this->patchJson("/api/genres/{$requestedGenre->id}", [
            'title' => $newGenreTitle,
        ]);

        $response->assertUnauthorized();

        // Проверка, если пользователь аутентифицирован, но не модератор
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->actingAs($user)->patchJson("/api/genres/{$requestedGenre->id}", [
            'title' => $newGenreTitle,
        ]);

        $response->assertForbidden();

        // Проверка, если пользователь - модератор
        $moderator = Sanctum::actingAs(User::factory()->moderator()->create());

        // а) Проверка работы при введении старых данных
        $response = $this->actingAs($moderator)->patchJson("/api/genres/{$requestedGenre->id}", [
            'title' => $requestedGenre->title,
        ]);

        $response->assertStatus(Response::HTTP_ACCEPTED);

        // Проверка данных жанра
        $genre = Genre::find($requestedGenre->id);
        $this->assertEquals($genreTitle, $genre->title);

        // б) Проверка работы при введении новых данных жанра
        $response = $this->actingAs($moderator)->patchJson("/api/genres/{$requestedGenre->id}", [
            'title' => $newGenreTitle,
        ]);

        $response->assertStatus(Response::HTTP_ACCEPTED);

        // Проверка данных жанра на соответствие нововведённым
        $genre = Genre::find($requestedGenre->id);
        $this->assertEquals($newGenreTitle, $genre->title);

        // в) Проверка работы при попытке присвоить название уже существующего жанра (оно должно быть уникальным)
        $response = $this->actingAs($moderator)->patchJson("/api/genres/{$requestedGenre->id}", [
            'title' => $existingGenreTitle,
        ]);

        $response->assertUnprocessable();

        // Проверка, что данные жанра не изменились
        $genre = Genre::find($requestedGenre->id);
        $this->assertEquals($newGenreTitle, $genre->title);
    }
}
