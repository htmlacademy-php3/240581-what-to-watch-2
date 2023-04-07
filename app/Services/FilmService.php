<?php

namespace App\services;

use App\Repositories\MovieRepositoryInterface;
use App\repositories\ImdbHtmlAcademyRepository;
use App\Models\Film;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

/**
 * Прикладной сервис MovieService,
 * используя MovieRepositoryInterface осуществляет все операции с сущностью Movie
 * @property MovieRepositoryInterface $movieRepository
 */
class FilmService
{
    /**
     * Метод поиска или создания, в случае отсутствия в БД,
     * моделей классов Actor и Genre с переданным именем класса по именам,
     * перечисленным в $data и выдачей массива с id этих моделей.
     *
     * @param  string $className - имя класса
     * @param  array $modelNames - массив с именами искомых (создаваемых) моделей
     * @param  string $separator - разделитель данных в переданной строке данными
     *
     * @return array|\Exception - массив с id созданных или найденных моделей
     */
    private function findOrCreateModelsAndGetIds(string $className, array $modelNames): array|\Exception
    {
        $modelId = [];
        $name = 'name';

        if ('Genre' === $className) {
            $name = 'title';
        }

        $modelClass = 'App\\Models\\' . $className;

        foreach ($modelNames as $modelName) {
            $modelId[] = app($modelClass)::firstOrCreate(["{$name}" => $modelName])->id;
        }

        return $modelId;
    }

    public function __construct(
        private MovieRepositoryInterface $movieRepository = new ImdbHtmlAcademyRepository(new Client()),
    ) {
    }

    /**
     * Метод поиска фильма по его id в базе данных OMDB (https://www.omdbapi.com/)
     * @param  string $imdbId - id фильма в базе данных OMDB
     *
     * @return array|null - массив с информацией о фильме,
     * полученный из базы данных OMDB через конкретную реализацию интерфейса репозитория MovieRepositoryInterface
     */
    public function searchFilm(string $imdbId): array
    {
        return $this->movieRepository->findById($imdbId);
    }

    /**
     * Метод создания модели класса Film
     *
     * @param  array $filmData - массив с данными фильма из базы данных OMDB
     *
     * @return Film|null - вновьсозданная, несохранённая в БД модель класса Film
     */
    private function createFilm(array $filmData): Film
    {
        return new Film([
            'title' => $filmData['name'],
            'poster_image' => $filmData['poster'],
            'description' => $filmData['desc'],
            'director' => $filmData['director'],
            'run_time' => $filmData['run_time'],
            'released' => $filmData['released'],
            'imdb_id' => $filmData['imdb_id'],
            'status' => FILM::PENDING,
            'video_link' => $filmData['video'],
        ]);
    }

    /**
     * Метод добавления фильма в базу
     *
     * @param  array $filmData - массив с данными фильма из базы данных OMDB
     *
     * @return mixed - либо void, если транзакция успешна,
     * либо сообщение об ошибке
     */
    public function saveFilm(array $filmData) //: mixed
    {
        try {
            DB::beginTransaction();


            $actorsId = $this->findOrCreateModelsAndGetIds('Actor', $filmData['actors']);
            $genresId = $this->findOrCreateModelsAndGetIds('Genre', $filmData['genres']);

            $film = $this->createFilm($filmData);
            $film->save();

            $film->actors()->attach($actorsId);
            $film->genres()->attach($genresId);


            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }
}
