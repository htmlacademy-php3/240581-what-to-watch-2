<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use \App\Services\FilmService;
use \App\Jobs\AddFilmJob;
use App\Models\Film;
use Mockery\MockInterface;
use App\Repositories\MovieRepositoryInterface;

class AddFilmJobTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Проверка метода handle класса AddFilmJob по добавлению фильма в базу
     *
     * @return void
     */
    public function test_adding_a_film_to_the_database()
    {
        $imdbId = 'tt0000000';

        // Фейковые данные для имитации ответа репозитория
        $filmData = [
            'name' => 'name',
            'poster' => 'poster',
            'desc' => 'desc',
            'director' => 'director',
            'run_time' => 120,
            'released' => 2010,
            'imdb_id' => 'tt0111161',
            'video' => 'video',
            'actors' => ['actor1', 'actor2', 'actor3'],
            'genres' => ['genre1', 'genre2']
        ];

        // Эталонные данные. С такими атрибутами должен быть создан и добавлен в БД тестовый фильм
        $referenseFilmAttributes = [
            'title' => $filmData['name'],
            'poster_image' => $filmData['poster'],
            'description' => $filmData['desc'],
            'director' => $filmData['director'],
            'run_time' => $filmData['run_time'],
            'released' => $filmData['released'],
            'imdb_id' => $filmData['imdb_id'],
            'status' => FILM::PENDING,
            'video_link' => $filmData['video'],
        ];

        $mockRepository = $this->mock(MovieRepositoryInterface::class, function (MockInterface $mockRepository) use ($filmData) {
            $mockRepository->shouldReceive('findById')->once()->andReturn($filmData);
        });

        $this->mock(FilmService::class, function (MockInterface $mockService) use ($filmData) {
            $mockService->shouldReceive('searchFilm')->andReturn($filmData);
        });

        $addFilmJob = new AddFilmJob($imdbId, $mockRepository);
        $addFilmJob->handle();

        // Проверка, что в базе данных появились записи: 1 фильма, 3-х актёров, 2-х жанров
        $this->assertDatabaseCount('films', 1);
        $this->assertDatabaseCount('actors', 3);
        $this->assertDatabaseCount('genres', 2);

        // Проверка наличия эталонных атрибутов в созданных записях таблиц: 'films', 'actors' и 'genres'
        $this->assertDatabaseHas('films', $referenseFilmAttributes);

        $this->assertDatabaseHas('actors', ['name' => 'actor1', 'name' => 'actor2', 'name' => 'actor3']);

        $this->assertDatabaseHas('genres', ['title' => 'genre1', 'title' => 'genre1']);
    }

    /**
     * Проверка метода handle класса AddFilmJob по добавлению фильма в базу
     *
     * @return void
     */
    public function test_for_adding_a_task_to_the_queue()
    {
        Queue::fake();

        $imdbId = 'tt0000000';

        AddFilmJob::dispatch($imdbId);

        Queue::assertPushed(AddFilmJob::class);
    }
}
