<?php

namespace App\repositories;

use App\repositories\MovieRepositoryInterface;
//use backend\parameters\OpenMovieDatabase;

/**
 * Репозиторий The Open Movie Database для класса Movie
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
     * @param string $imdbId - Действительный идентификатор IMDb в The Open Movie Database (например, tt1285016)
     *
     * @return array|null - массив с информацией о фильме (в последствии будет заменён на модель класса Movie), полученный из базы данных OMDB через конкретную реализацию интерфейса репозитория MovieRepositoryInterface
     *
     */
    public function findById(string $imdbId): ?array
    {
        $query = [
            'i' => $imdbId,
            'apikey' => '8aadef61',
        ];

        $response = $this->httpClient->request('GET', self::OMDB_URI, ['query' => $query]);

        $body = $response->getBody();

        $movieInfo = json_decode($body->getContents(), true);

        if (array_key_exists('Error', $movieInfo)) {
            return null;
        }

        // TODO: $movie = new Movie(); Заполнение полученными данными из movieInfo

        return $movieInfo; // TODO: return $movie;
    }
}
