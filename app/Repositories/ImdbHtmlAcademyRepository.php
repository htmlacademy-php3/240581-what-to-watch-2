<?php

namespace App\repositories;

use App\repositories\MovieRepositoryInterface;

/**
 * Репозиторий для класса Film, работающий с учебным сервисом IMDB Proxi (http://guide.phpdemo.ru/api/documentation)
 *
 * @property $httpClient - http-клиент
 *
 * @return array|null - массив с информацией о фильме или null, если информация не найдена
 */
class ImdbHtmlAcademyRepository implements MovieRepositoryInterface
{
    private const IMDB_URI = 'http://guide.phpdemo.ru/api/films/';

    private $httpClient;

    public function __construct($httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Метод поиска фильма по его id в базе данных IMDB Proxi
     * (http://guide.phpdemo.ru/api/films/)
     *
     * @param  string $imdbId - Действительный идентификатор IMDb в MDB Proxi (tt0111161)
     *
     * @return array|null - массив с информацией о фильме, полученный из базы данных OMDB через конкретную реализацию интерфейса репозитория MovieRepositoryInterface
     *
     */
    public function findById(string $imdbId): ?array
    {
        $response = $this->httpClient->request('GET', self::IMDB_URI . $imdbId);

        $body = $response->getBody();

        return json_decode($body->getContents(), true);
    }
}
