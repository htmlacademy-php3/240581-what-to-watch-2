<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use \App\Models\Favorite;
use \App\Models\Film;
use \App\Models\User;

class FavoriteControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест action index() FavoriteController`а
     *
     * @return void
     */
    public function test_index()
    {
        // Количество избранных фильмов у пользователя
        $filmsCount = 15;

        // Текущая страница в ответе согласно ТЗ
        $currentPage = 1;

        // Количество выводимых моделей на одной странице согласно ТЗ
        $paginateCount = 8;


        // Проверка, если пользователь неаутентифицирован
        User::factory()->has(Film::factory($filmsCount))->create();

        $response = $this->getJson('/api/favorite');

        $response->assertUnauthorized();

        // Проверка, если пользователь аутентифицирован
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->actingAs($user)->getJson('/api/favorite');

        // Проверка, что по его запросу приходят только его фильмы.
        $response
            ->assertOk()
            // Проверка, что возвращено 0 фильмов и текущая страница - "1"
            ->assertJsonCount(0, 'data.*')
            ->assertJsonFragment(['current_page' => $currentPage]);

        // Генерация авторизированного пользователя с избранными им фильмами.
        $user = Sanctum::actingAs(User::factory()->has(Film::factory($filmsCount))->create());

        $response = $this->actingAs($user)->getJson('/api/favorite');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'poster_image',
                        'preview_video_link'
                    ]
                ],
                'first_page_url',
                'next_page_url',
                'prev_page_url',
                'per_page',
                'total',
            ])
            // Проверка, что возвращено 8 фильмов (пагинация), текущая страница "1" и найдены все фильмы
            ->assertJsonCount($paginateCount, 'data.*')
            ->assertJsonFragment([
                'current_page' => $currentPage,
                'total' => $filmsCount
            ]);

        // Проверка сортировки по дате выхода фильмов: от новых к старым (desc)
        $responseData = $response->json()['data'];

        $released = null;

        foreach ($responseData as $element) {
            $film = Film::find($element['id']);

            if ($released && isset($film->released)) {
                $parameter1 = $released;
                $parameter2 = $film->released;

                assert($parameter1 >= $parameter2);
            }
            $released = $film->released;
        }
    }

    /**
     * Тест action store() FavoriteController`а
     *
     * @return void
     */
    public function test_store()
    {
        // Проверка, если пользователь неаутентифицирован
        $film = Film::factory()->create();
        User::factory()->create();

        $response = $this->postJson("/api/films/{$film->id}/favorite");

        $response->assertUnauthorized();

        // Проверка, если пользователь аутентифицирован и фильма в избранном ещё нет
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson("/api/films/{$film->id}/favorite");

        $response
            ->assertCreated()
            ->assertJsonStructure([
                'id',
                'film_id',
                'user_id',
            ])
            ->assertJsonFragment([
                'film_id' => $film->id,
                'user_id' => $user->id,
            ]);

        // Проверка, если пользователь аутентифицирован, а фильм уже находится в списке избранного
        $response = $this->postJson("/api/films/{$film->id}/favorite");

        $response
            ->assertUnprocessable()
            ->assertJsonStructure([
                'message',
            ])
            ->assertJsonFragment([
                'message' => 'Этот фильм уже присутствует в Вашем списке',
            ]);
    }

    /**
     * Тест action destroy() FavoriteController`а
     *
     * @return void
     */
    public function test_destroy()
    {
        // Проверка, если пользователь неаутентифицирован
        $film = Film::factory()->create();

        User::factory()->create();

        $response = $this->deleteJson("/api/films/{$film->id}/favorite");

        $response->assertUnauthorized();

        // Проверка, если пользователь аутентифицирован, фильм находится в избранном
        $user = Sanctum::actingAs(User::factory()->create());

        Favorite::factory()->state([
            'film_id' => $film->id,
            'user_id' => $user->id,
        ])->create();

        $response = $this->deleteJson("/api/films/{$film->id}/favorite");

        $response
            ->assertNoContent();

        // Проверка, если пользователь аутентифицирован, фильм не находится в списке избранного
        $response = $this->deleteJson("/api/films/{$film->id}/favorite");

        $response
            ->assertUnprocessable()
            ->assertJsonStructure([
                'message',
            ])
            ->assertJsonFragment([
                'message' => 'Этот фильм отсутствует в Вашем списке',
            ]);
    }
}
