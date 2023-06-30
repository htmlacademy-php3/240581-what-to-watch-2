<?php

namespace App\Repositories;

use App\Repositories\ExternalCommentRepositoryInterface;
use Carbon\Carbon;

/**
 * Репозиторий The Open Movie Database для класса Film
 *
 * @property $httpClient - http-клиент
 *
 * @return array|null - массив с информацией о фильме или null, если информация не найдена
 */
class ImdbProxyCommentRepository implements ExternalCommentRepositoryInterface
{
    private const IMDB_PROXY_URI = 'http://guide.phpdemo.ru/api/comments/';

    private $httpClient;

    public function __construct($httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Метод получения всех комментариев в базе данных imdb proxy (http://guide.phpdemo.ru/api)
     *
     * @return array|null - массив с комментариями, созданными после даты, определённой в $cutoffDate
     */
    public function findAllNew(): array|null
    {
        $cutoffDate = Carbon::now()->subDay()->toDateTimeLocalString();

        $query = [
            'after' => $cutoffDate,
        ];

        $response = $this->httpClient->request('GET', self::IMDB_PROXY_URI, ['query' => $query]);

        $body = $response->getBody();

        return json_decode($body->getContents(), true);
    }
}
