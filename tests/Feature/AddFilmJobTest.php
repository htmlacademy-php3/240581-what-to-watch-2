<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use \App\Services\FilmService;
use \App\Jobs\AddFilmJob;
use App\Models\Actor;
use App\Models\Film;
use App\Models\Genre;
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
        $referenseFilm = Film::factory()->make()->toArray();
        $referenseActors = Actor::factory(3)->make();
        $referenseGenres = Genre::factory(2)->make();

        $referenseFilm['actors'] = [];
        $referenseFilm['genres'] = [];

        foreach ($referenseActors as $referenseActor) {
            $referenseFilm['actors'][] = $referenseActor->name;
        }

        foreach ($referenseGenres as $referenseGenre) {
            $referenseFilm['genres'][] = $referenseGenre->title;
        }

        $mockRepository = $this->mock(MovieRepositoryInterface::class, function (MockInterface $mockRepository) use ($referenseFilm) {
            $mockRepository->shouldReceive('findById')->once()->andReturn($referenseFilm);
        });

        $this->mock(FilmService::class, function (MockInterface $mockService) use ($referenseFilm) {
            $mockService->shouldReceive('searchFilm')->andReturn($referenseFilm);
        });

        $imdbId = $referenseFilm['imdb_id'];

        $addFilmJob = new AddFilmJob($imdbId, $mockRepository);
        $addFilmJob->handle();

        // Проверка, что в базе данных появились записи: 1 фильма, 3-х актёров, 2-х жанров
        $this->assertDatabaseCount('films', 1);
        $this->assertDatabaseCount('actors', 3);
        $this->assertDatabaseCount('genres', 2);

        // Удаляем ключи, которых нет в таблице 'films'
        unset($referenseFilm['actors']);
        unset($referenseFilm['genres']);

        // Проверка наличия эталонных атрибутов в созданных записях таблиц: 'films', 'actors' и 'genres'
        $film = Film::first()->toArray();

        foreach ($referenseFilm as $key => $value) {
            $this->assertArrayHasKey($key, $film);
        }

        foreach ($referenseActors as $actor) {
            $this->assertDatabaseHas('actors', ['name' => $actor['name']]);
        }

        foreach ($referenseGenres as $genre) {
            $this->assertDatabaseHas('genres', ['title' => $genre['title']]);
        }
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
