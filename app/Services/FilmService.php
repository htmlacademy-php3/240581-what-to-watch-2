<?php

namespace App\Services;

use App\Repositories\MovieRepositoryInterface;
use App\repositories\OmdbMovieRepository;
use App\Models\Actor;
use App\Models\Comment;
use App\Models\Film;
use App\Models\Genre;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Прикладной сервис MovieService,
 * используя MovieRepositoryInterface осуществляет все операции с сущностью Movie
 *
 * @property MovieRepositoryInterface $movieRepository
 */
class FilmService
{
    public function __construct(
        private $movieRepository = new OmdbMovieRepository(new Client())
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

    /**
     * Метод определения, находится ли фильм в списках избранного у пользователя
     * @param  Film $film - модель класса Film
     * @param  User $user - пользователь
     *
     * @return bool - массив с информацией о фильме,
     * полученный из базы данных OMDB через конкретную реализацию интерфейса репозитория MovieRepositoryInterface
     */
    public static function isFavorite(Film $film, User $user): bool
    {
        $favoriteUsers = $film->users;
        foreach ($favoriteUsers as $favoriteUser)
            if ($favoriteUser->id === $user->id) {
                return true;
            }
        return false;
    }

    /**
     * Метод создания запроса в БД на получение списка фильмов согласно парамертам в $request
     * @param  Request $request
     *
     * @return Illuminate\Database\Eloquent\Builder $query - экземпляр построителя запросов в БД, сформированный согласно входным параметрам
     */
    public static function createRequestForFilmsByParameters(Request $request): Builder
    {
        $orderTo = 'desc';
        $orderBy = 'released';
        $status  = 'ready';

        if (isset(Auth::user()->is_moderator) && Auth::user()->is_moderator && $request->status) {
            $status = $request->status;
        }

        if (isset($request->order_to)) {
            $orderTo = $request->order_to;
        }

        if (isset($request->order_by)) {
            $orderBy = $request->order_by;
        }

        $query = Film::where('status', $status);

        if (isset($request->genre)) {
            $genre = Genre::where('title', $request->genre)->first();
            $filmIds = $genre->films->modelKeys();
            $query = $query->whereIn('id', $filmIds);
        }

        if (isset($request->order_by) && $request->order_by === 'rating') {
            $orderBy = Comment::selectRaw('avg(c.rating)')->from('comments as c')->whereColumn('c.film_id', 'films.id');
        }

        $query->orderBy($orderBy, $orderTo);

        return $query;
    }
}
