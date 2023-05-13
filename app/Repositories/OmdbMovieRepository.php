<?php

namespace App\repositories;

use App\repositories\MovieRepositoryInterface;
use Illuminate\Support\Str;

/**
 * Репозиторий The Open Movie Database для класса Film
 *
 * @property $httpClient - http-клиент
 *
 * @return array|null - массив с информацией о фильме или null, если информация не найдена
 */
class OmdbMovieRepository implements MovieRepositoryInterface
{
    private const OMDB_URI = 'http://www.omdbapi.com/';

    private $httpClient;

    public function __construct($httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Метод поиска фильма по его id в базе данных OMDB (https://www.omdbapi.com/)
     * @param  string $imdbId - Действительный идентификатор IMDb в The Open Movie Database (например, tt1285016)
     *
     * @return array|null - массив с информацией о фильме, полученный из базы данных OMDB через конкретную реализацию интерфейса репозитория MovieRepositoryInterface
     *
     */
    public function findById(string $imdbId): array|null
    {
        $query = [
            'i' => $imdbId,
            'apikey' => '8aadef61',
        ];

        $response = $this->httpClient->request('GET', self::OMDB_URI, ['query' => $query]);

        $body = $response->getBody();

        $data = json_decode($body->getContents(), true);

        if (isset($data['Response']) && 'False' === $data['Response']) {
            return null;
        }
        return $this->convertFilmDataToFormat($data);
    }

    /**
     * Метод получения массива из строки ответа с перечислениями
     *
     * @param  string $responseString
     *
     * @return array - массив с элементами, перечисленными в строке ответа
     */
    private function getArrayFromResponseString(string $responseString): array
    {
        if (!$responseString || 'N/A' === $responseString) {
            return [];
        }
        return Str::of($responseString)->explode(', ')->toArray();
    }

    /**
     * Метод приведения полученных данных фильма из базы данных OMDB к формату БД приложения
     *
     * @param  array $data - массив с данными фильма из базы данных OMDB
     *
     * @return array массив с данными фильма из базы данных OMDB, приведёнными к формату БД приложения
     */
    private function convertFilmDataToFormat(array $data): array
    {
        if ('N/A' === $data['Year']) {
            $data['Year'] = null;
        }

        return [
            'name' => $data['Title'],
            'poster_image' => $data['Poster'],
            'description' => $data['Plot'],
            'director' => $data['Director'],
            'run_time' => intval($data['Runtime']),
            'released' => $data['Year'],
            'imdb_id' => $data['imdbID'],
            'actors' => $this->getArrayFromResponseString($data['Actors']),
            'genres' => $this->getArrayFromResponseString($data['Genre']),
        ];
    }
}
