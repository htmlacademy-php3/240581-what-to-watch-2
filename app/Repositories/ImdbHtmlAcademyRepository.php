<?php

namespace App\repositories;

use App\repositories\MovieRepositoryInterface;

use function PHPUnit\Framework\arrayHasKey;

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

    // Массив с отличающимися от полей таблицы 'films' ключами.
    private array $names = [
        'title' => 'name',
        'poster_image' => 'poster',
        'description' => 'desc',
        'video_link' => 'video',
    ];

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

        $filmData = json_decode($body->getContents(), true);

        $filmData = $this->renameKeyName($this->names, $filmData);

        return $filmData;
    }

    /**
     * Метод для приведения имени ключа массива в соответствие с именем в таблице 'films'
     *
     * @param  array $names - массив, где ключ - имя ключа в массиве $filmData, которое надо поменять, а значение - новое имя ключа
     * @param  array $filmData - массив с данными фильма c ключом названия фильма 'name'
     *
     * @return array $filmData - массив с данными фильма c ключом названия фильма 'title'
     */
    private function renameKeyName(array $names, array $filmData): array
    {
        foreach ($names as $key => $value) {
            if (array_key_exists($value, $filmData)) {
                $filmData[$key] = $filmData[$value];
                unset($filmData[$value]);
            }
        }

        return $filmData;
    }
}
