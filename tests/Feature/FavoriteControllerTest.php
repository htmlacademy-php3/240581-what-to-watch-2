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
    /**
     * Тест action index() FavoriteController`а
     *
     * @return void
     */
    public function test_index()
    {
        // Проверка, если пользователь неаутентифицирован
        $unloggedUser = User::factory()->create();

        Film::factory(20)->create();

        Favorite::factory()
            ->count(5)
            ->state(new Sequence(
                fn ($sequence) => ['film_id' => Film::all()->random(), 'user_id' => $unloggedUser],
            ))
            ->create();

        $response = $this->getJson('/api/favorite');

        $response->assertUnauthorized();

        // Проверка, если пользователь аутентифицирован
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->actingAs($user)->getJson('/api/favorite');

        // Проверка, что по его запросу приходят только его фильмы.
        $response
            ->assertOk()
            // Проверка, что возвращено 0 фильмов
            ->assertJsonCount(0, 'data.*')
            ->assertJsonFragment(['current_page' => 1]);

        // Генерация избранных фильмов для зарегистрированного пользователя.
        Favorite::factory()
            ->count(15)
            ->state(new Sequence(
                fn ($sequence) => ['film_id' => Film::all()->random(), 'user_id' => $user],
            ))
            ->create();

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
            // Проверка, что возвращено 8 фильмов
            ->assertJsonCount(8, 'data.*')
            ->assertJsonFragment(['current_page' => 1]);

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
}
