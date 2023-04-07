<?php

namespace App\repositories;

use App\repositories\MovieRepositoryInterface;

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
    public function findById(string $imdbId): ?array
    {
        $query = [
            'i' => $imdbId,
            'apikey' => '8aadef61',
        ];

        $response = $this->httpClient->request('GET', self::OMDB_URI, ['query' => $query]);

        $body = $response->getBody();

        return json_decode($body->getContents(), true);
    }
}
