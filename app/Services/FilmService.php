<?php

namespace App\services;

use App\Repositories\MovieRepositoryInterface;
use App\repositories\ImdbHtmlAcademyRepository;
use App\Models\Actor;
use App\Models\Film;
use App\Models\Genre;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Прикладной сервис MovieService,
 * используя MovieRepositoryInterface осуществляет все операции с сущностью Movie
 * @property MovieRepositoryInterface $movieRepository
 */
class FilmService
{
    public function __construct(
        // Закомментировал "readonly MovieRepositoryInterface", т.к. при тестировании на имитируемый репозиториц созданный Mockery\MockInterface выдаёт ошибку ожидаемого типа
        private /*readonly MovieRepositoryInterface*/ $movieRepository = new ImdbHtmlAcademyRepository(new Client()),
    ) {
    }

    /**
     * Метод поиска фильма по его id в базе данных OMDB (https://www.omdbapi.com/)
     * @param  string $imdbId - id фильма в базе данных OMDB
     *
     * @return array|null - массив с информацией о фильме,
     * полученный из базы данных OMDB через конкретную реализацию интерфейса репозитория MovieRepositoryInterface
     */
    public function searchFilm(string $imdbId): array|null
    {
        return $this->movieRepository->findById($imdbId);
    }

    /**
     * Метод создания модели класса Film
     *
     * @param  array $filmData - массив с данными фильма из базы данных OMDB
     *
     * @return Film - вновьсозданная, несохранённая в БД модель класса Film
     */
    private function createFilm(array $filmData): Film
    {
        return new Film([
            'title' => $filmData['title'],
            'poster_image' => $filmData['poster_image'],
            'description' => $filmData['description'],
            'director' => $filmData['director'],
            'run_time' => $filmData['run_time'],
            'released' => $filmData['released'],
            'imdb_id' => $filmData['imdb_id'],
            'status' => FILM::PENDING,
            'video_link' => $filmData['video_link'],
        ]);
    }

    /**
     * Метод сохранения фильма в базе
     *
     * @param  array $filmData - массив с данными фильма из базы данных OMDB
     *
     * @return void
     */
    public function saveFilm(array $filmData): void
    {
        try {
            $actorsId = [];
            $genresId = [];
            $actors = $filmData['actors'];
            $genres = $filmData['genres'];

            DB::beginTransaction();

            if (is_iterable($actors)) {
                foreach ($actors as $actor) {
                    $actorsId[] = Actor::firstOrCreate(['name' => $actor])->id;
                }
            }

            if (is_iterable($genres)) {
                foreach ($genres as $genre) {
                    $genresId[] = Genre::firstOrCreate(['title' => $genre])->id;
                }
            }

            $film = $this->createFilm($filmData);
            $film->save();

            $film->actors()->attach($actorsId);
            $film->genres()->attach($genresId);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::warning($exception->getMessage());
        }
    }
}
