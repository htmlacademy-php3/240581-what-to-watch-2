<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use \App\Models\Film;
use \App\Models\Genre;
use \App\Models\User;

class FilmControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Метод проверки правильности сортировки полученных фильмов в соответствии с параметром запроса
     *
     * @param  string $parameterName - имя свойства или метода, соответствующие указанному параметру в запросе
     * @param  TestResponse $response
     * @param  string $sortBy - порядок сортировки
     *
     * @return void
     */
    protected function checkingFilmsByParameters($parameterName, TestResponse $response, $sortBy = 'desc'): void
    {
        $responseData = $response->json()['data'];

        $value = null;

        foreach ($responseData as $element) {
            $film = Film::find($element['id']);

            if ($value && isset($film->{$parameterName})) {
                $parameter1 = $value;
                $parameter2 = $film->{$parameterName};

                if ($sortBy === 'asc') {
                    $parameter1 = $film->{$parameterName};
                    $parameter2 = $value;
                }
                assert($parameter1 >= $parameter2);
            }
            $value = $film->{$parameterName};
        }
    }

    /**
     * Метод проверки правильности фильтрации полученных фильмов по жанру, указанному в запросе
     *
     * @param  Genre $referenceGenre
     * @param  TestResponse $response
     *
     * @return void
     */
    protected function checkingFilmsGenre(Genre $referenceGenre, TestResponse $response): void
    {
        $responseData = $response->json()['data'];

        foreach ($responseData as $element) {
            $film = Film::find($element['id']);
            $genres = $film->genres;
            $genres = $genres->toArray();

            // Проверка, что среди полученных фильмов нет не имеющих искомый жанр
            $presence = false;
            foreach ($genres as $el) {
                if (in_array($referenceGenre->title, $el, true)) {
                    $presence = true;
                }
            }
            assert($presence, true);
        }
    }

    /**
     * Метод проверки правильности фильтрации полученных фильмов по статусу, указанному в запросе
     *
     * @param  string $statusName - статус фильма, по умолчанию значение ready
     * Пользователь с ролью 'модератор' может изменить значение на: 'pending', 'on moderate'
     * @param  TestResponse $response
     * @param  null|User $user
     *
     * @return void
     */
    protected function checkingFilmsStatus(string $statusName, TestResponse $response, User $user = null): void
    {
        $responseData = $response->json()['data'];
        $status = 'ready';

        if ($user && isset($user->is_moderator) && $user->is_moderator) {
            $status = $statusName;
        }

        foreach ($responseData as $element) {
            $film = Film::find($element['id']);
            $this->assertEquals($status, $film->status);
        }
    }

    /**
     * Метод проверки правильности фильтрации полученных фильмов по статусу, указанному в запросе,
     * с проверкой правильности сортировки
     *
     * @param  string $orderBy - правило сортировки. Возможные значения: released, rating
     * Пользователь с ролью 'модератор' может изменить значение на: 'pending', 'on moderate'
     *
     * @param  array $ordersTo
     *
     * @return void
     */
    protected function checkingFilmsWithSorted(string $orderBy, array $ordersTo = ['', 'asc']): void
    {
        $parameter = $orderBy;

        if ($orderBy === 'rating') {
            $parameter = 'getTotalRating()';
        }

        foreach ($ordersTo as $orderTo) {
            $response = $this->json('GET', '/api/films', ['order_by' => $orderBy, 'order_to' => $orderTo]);

            $this->checkingFilmsByParameters($parameter, $response, $orderTo);
        }
    }


    /**
     * Тест action index() FilmController`а для незалогиненного пользователя (Guest).
     *
     * @return void
     */
    public function test_index_for_unlogged_user()
    {
        // Запустить `DatabaseSeeder`
        $this->seed();

        // Количество возвращённых фильмов
        $count = 8;

        // Направление сортировки. По умолчанию: desc
        $ordersTo = ['', 'asc'];

        $response = $this->getJson('/api/films');

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
            ->assertJsonCount($count, 'data.*')
            ->assertJsonFragment(['current_page' => 1]);

        // Проверка сортировки фильмов по умолчанию по дате выхода: от новых к старым (desc)
        $this->checkingFilmsByParameters('released', $response);

        // Проверка сортировки фильмов по дате выхода: от старых к новым (asc)
        $orderTo = 'asc';

        $response = $this->json('GET', '/api/films', ['order_to' => $orderTo]);

        $this->checkingFilmsByParameters('released', $response, $orderTo);

        // Проверка сортировки фильмов по рейтингу, от большего к меньшему (desc)
        $this->checkingFilmsWithSorted('rating');

        // Проверка фильтрации фильмов по жанру c сортировкой по умолчанию по дате выхода: от новых к старым (desc)
        $referenceGenre = Genre::inRandomOrder()
            ->first();

        foreach ($ordersTo as $orderTo) {
            $response = $this->json('GET', '/api/films', ['genre' => $referenceGenre->title, 'order_to' => $orderTo]);

            $this->checkingFilmsByParameters('released', $response, $orderTo);
            $this->checkingFilmsGenre($referenceGenre, $response);
        }

        $statuses = [
            'pending',
            'on moderation'
        ];

        foreach ($statuses as $status) {
            foreach ($ordersTo as $orderTo) {
                $response = $this->json('GET', '/api/films', ['status' => $status, 'order_to' => $orderTo]);

                $this->checkingFilmsByParameters('released', $response, $orderTo);
                $this->checkingFilmsStatus($status, $response);
            }
        }
    }

    /**
     * Тест action index() FilmController`а для залогиненных пользователя и модератора.
     *
     * @return void
     */
    public function test_index_for_logged_user()
    {
        // Запустить `DatabaseSeeder`
        $this->seed();

        // Количество возвращённых фильмов
        $count = 8;

        // Направление сортировки. По умолчанию: desc
        $ordersTo = ['', 'asc'];

        $testUsers = [
            // Аутентифицированный пользователь
            Sanctum::actingAs(User::factory()->create()),
            // Модератор
            Sanctum::actingAs(User::factory()->moderator()->create())
        ];

        foreach ($testUsers as $testUser) {
            $response = $this->getJson('/api/films');

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
                ->assertJsonCount($count, 'data.*')
                ->assertJsonFragment(['current_page' => 1]);

            // Проверка сортировки фильмов по умолчанию по дате выхода: от новых к старым (desc)
            $this->checkingFilmsByParameters('released', $response);

            // Проверка сортировки фильмов по дате выхода: от старых к новым (asc)
            $orderTo = 'asc';

            $response = $this->json('GET', '/api/films', ['order_to' => $orderTo]);

            $this->checkingFilmsByParameters('released', $response, $orderTo);

            // Проверка сортировки фильмов по рейтингу, от большего к меньшему (desc)
            $this->checkingFilmsWithSorted('rating');

            // Проверка фильтрации фильмов по жанру c сортировкой по умолчанию по дате выхода: от новых к старым (desc)
            $referenceGenre = Genre::inRandomOrder()
                ->first();

            foreach ($ordersTo as $orderTo) {
                $response = $this->json('GET', '/api/films', ['genre' => $referenceGenre->title, 'order_to' => $orderTo]);

                $this->checkingFilmsByParameters('released', $response, $orderTo);
                $this->checkingFilmsGenre($referenceGenre, $response);
            }

            $statuses = [
                'pending',
                'on moderation'
            ];

            foreach ($statuses as $status) {
                foreach ($ordersTo as $orderTo) {
                    $response = $this->actingAs($testUser)->json('GET', '/api/films', ['status' => $status, 'order_to' => $orderTo]);

                    $this->checkingFilmsByParameters('released', $response, $orderTo);
                    $this->checkingFilmsStatus($status, $response, $testUser);
                }
            }
        }
    }
}
