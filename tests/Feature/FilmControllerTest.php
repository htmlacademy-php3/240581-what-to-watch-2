<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\TestResponse;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use \App\Models\Film;
use \App\Models\Genre;
use \App\Models\User;
use App\Models\Favorite;

class FilmControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Метод проверки правильности сортировки полученных фильмов в соответствии с параметром запроса
     *
     * @param  string $parameterName - имя свойства или метода, соответствующие указанному параметру в запросе
     * @param  TestResponse $response
     * @param  string $sortBy - порядок сортировки
     *
     * @return void
     */
    private function checkingFilmsByParameters($parameterName, TestResponse $response, $sortBy = 'desc'): void
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
    private function checkingFilmsGenre(Genre $referenceGenre, TestResponse $response): void
    {
        $responseData = $response->json()['data'];

        foreach ($responseData as $element) {
            $film = Film::find($element['id']);
            $genres = $film->genres;
            $genres = $genres->toArray();

            // Проверка, что среди полученных фильмов все имеют искомый жанр
            $presence = false;
            foreach ($genres as $el) {
                if (in_array($referenceGenre->title, $el, true)) {
                    $presence = true;
                }
            }
            assert($presence);
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
    private function checkingFilmsStatus(string $statusName, TestResponse $response, User $user = null): void
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
     * @param  array $ordersTo - тип сортировки
     *
     * @return void
     */
    private function checkingFilmsWithSorted(string $orderBy, array $ordersTo = ['', 'asc']): void
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

        // Проверка сортировки фильмов по рейтингу, от большего к меньшему (desc) и наоборот (asc)
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
            Film::FILM_STATUS_MAP['pending'],
            Film::FILM_STATUS_MAP['on moderation'],
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

            // Проверка сортировки фильмов по рейтингу (desc и asc)
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
                Film::FILM_STATUS_MAP['pending'],
                Film::FILM_STATUS_MAP['on moderation'],
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

    /**
     * Тест action show() FilmController`а.
     *
     * @return void
     */
    public function test_show()
    {
        // Проверка на возврат 404 ошибки в случае попытки обращения к несуществующему фильму

        // БД не наполнена, таблица 'films' пуста. Можно брать людой id
        $filmId = 1;

        // Проверим, что таблица в базе данных действительно не содержит записи фильма с таким id
        $this->assertDatabaseMissing('films', [
            'id' => $filmId,
        ]);

        $response = $this->getJson("/api/films/{$filmId}");
        $response->assertNotFound();

        // Наполняем БД, запустив `DatabaseSeeder`
        $this->seed();

        // Проверка ответа при запросе фильма неавторизованным пользователем
        $favorite = Favorite::inRandomOrder()->first();
        $film = $favorite->film;

        $response = $this->getJson("/api/films/{$film->id}");

        $response
            ->assertOk()
            ->assertJsonStructure([
                'id',
                'name',
                'poster_image',
                'preview_image',
                'background_image',
                'background_color',
                'video_link',
                'preview_video_link',
                'description',
                'rating',
                'scores_count',
                'director',
                'starring' => [],
                'run_time',
                'genre' => [],
                'released',
                'is_favorite'
            ])
            ->assertJsonFragment(['is_favorite' => []])
            ->assertJsonMissing(['is_favorite' => false])
            ->assertJsonMissing(['is_favorite' => true]);

        // Проверка, что 'is_favorite' содержит пустой массив
        $responseData = $response->json();

        assert(empty($responseData['is_favorite']));

        // Проверка ответа при запросе фильма, не находящимся у авторизованного пользователя в избранном

        // Создадим авторизованного пользователя, но не добавим ему связи с каким-либо фильмом
        $user = Sanctum::actingAs(User::factory()->create());

        $film = Film::inRandomOrder()->first();

        $response = $this->actingAs($user)->getJson("/api/films/{$film->id}");

        $response
            ->assertOk()
            ->assertJsonStructure([
                'id',
                'name',
                'poster_image',
                'preview_image',
                'background_image',
                'background_color',
                'video_link',
                'preview_video_link',
                'description',
                'rating',
                'scores_count',
                'director',
                'starring' => [],
                'run_time',
                'genre' => [],
                'released',
                'is_favorite'
            ])
            ->assertJsonFragment(['is_favorite' => false])
            ->assertJsonMissing(['is_favorite' => []])
            ->assertJsonMissing(['is_favorite' => true]);

        // Проверка ответа при запросе фильма, находящегося у авторизованного пользователя в избранном

        // Создадим связь созданного авторизованного пользователя с фильмом
        $favorite = Favorite::factory()
            ->state(new Sequence(
                fn ($sequence) => ['film_id' => $film, 'user_id' => $user],
            ))
            ->create();

        $response = $this->actingAs($user)->getJson("/api/films/{$film->id}");

        $response
            ->assertOk()
            ->assertJsonStructure([
                'id',
                'name',
                'poster_image',
                'preview_image',
                'background_image',
                'background_color',
                'video_link',
                'preview_video_link',
                'description',
                'rating',
                'scores_count',
                'director',
                'starring' => [],
                'run_time',
                'genre' => [],
                'released',
                'is_favorite'
            ])
            ->assertJsonFragment(['is_favorite' => true])
            ->assertJsonMissing(['is_favorite' => []])
            ->assertJsonMissing(['is_favorite' => false]);
    }

    /**
     * Проверка метода update() FilmController`а
     *
     * @return void
     */
    public function test_update()
    {
        $film = Film::factory()
            ->state([
                'imdb_id' => 'tt0944947',
            ])
            ->create();

        $reguestData = [
            'name' => $this->faker->sentence(),
            'imdb_id' => $film->imdb_id,
            'status' => FILM::FILM_STATUS_MAP['ready'],
            'poster_image' => $this->faker->imageUrl(),
            'preview_image' => $this->faker->imageUrl(),
            'background_image' => $this->faker->imageUrl(),
            'background_color' => '#ffffff',
            'video_link' => $this->faker->url(),
            'preview_video_link' => $this->faker->url(),
            'description' => $this->faker->text(),
            'director' => $this->faker->name(),
            'run_time' => $this->faker->numberBetween(30, 200),
            'released' => $this->faker->year(),
            'starring' => [$this->faker->name(), $this->faker->name(), $this->faker->name(), $this->faker->name(), $this->faker->name()],
            'genre' => [$this->faker->word()],
        ];

        // Проверка, если пользователь неаутентифицирован
        $response = $this->patchJson("/api/films/{$film->id}", $reguestData);

        $response->assertUnauthorized();

        // Проверка, если пользователь аутентифицирован, но не модератор
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->actingAs($user)->patchJson("/api/films/{$film->id}", $reguestData);

        $response->assertForbidden();

        // Проверка, если пользователь - модератор
        $moderator = Sanctum::actingAs(User::factory()->moderator()->create());

        $response = $this->actingAs($moderator)->patchJson("/api/films/{$film->id}", $reguestData);

        $response->assertStatus(Response::HTTP_ACCEPTED);

        // Проверка данных жанра
        $updatedFilm = Film::find($film->id);
        $this->assertEquals($reguestData['name'], $updatedFilm->name);
        $this->assertEquals($reguestData['status'], $updatedFilm->status);
        $this->assertEquals($reguestData['poster_image'], $updatedFilm->poster_image);
        $this->assertEquals($reguestData['preview_image'], $updatedFilm->preview_image);
        $this->assertEquals($reguestData['background_image'], $updatedFilm->background_image);
        $this->assertEquals($reguestData['background_color'], $updatedFilm->background_color);
        $this->assertEquals($reguestData['video_link'], $updatedFilm->video_link);
        $this->assertEquals($reguestData['preview_video_link'], $updatedFilm->preview_video_link);
        $this->assertEquals($reguestData['description'], $updatedFilm->description);
        $this->assertEquals($reguestData['director'], $updatedFilm->director);
        $this->assertEquals($reguestData['run_time'], $updatedFilm->run_time);
        $this->assertEquals($reguestData['released'], $updatedFilm->released);

        // Проверка "привязки" актеров
        $actorsName = [];
        foreach ($updatedFilm->actors as $actor) {
            $actorsName[] = $actor->name;
        }

        foreach ($reguestData['starring'] as $actor) {
            $this->assertTrue(in_array($actor, $actorsName));
        }

        // Проверка "привязки" жанров
        $genresName = [];
        foreach ($updatedFilm->genres as $genre) {
            $genresName[] = $genre->title;
        }

        foreach ($reguestData['genre'] as $genre) {
            $this->assertTrue(in_array($genre, $genresName));
        }

        // Проверка работы только с обязательным набором параметров
        $reguestData = [
            'name' => $this->faker->sentence(),
            'imdb_id' => $film->imdb_id,
            'status' => FILM::FILM_STATUS_MAP['pending'],
        ];

        $response = $this->actingAs($moderator)->patchJson("/api/films/{$film->id}", $reguestData);

        $response->assertStatus(Response::HTTP_ACCEPTED);

        // Проверка данных жанра
        $updatedFilm = Film::find($film->id);
        $this->assertEquals($reguestData['name'], $updatedFilm->name);
        $this->assertEquals($reguestData['status'], $updatedFilm->status);

        // Проверка работы при попытке обновления несуществующего фильма
        $nonExistentId = $film->id + 1;
        $reguestData['imdb_id'] = 'tt0944949';

        $response = $this->actingAs($moderator)->patchJson("/api/films/{$nonExistentId}", $reguestData);

        $response->assertNotFound();
    }
}
