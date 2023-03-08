<?php

namespace App\services;

use App\repositories\MovieRepositoryInterface;

/**
 * Прикладной сервис MovieService,
 * используя MovieRepositoryInterface осуществляет все операции с сущностью Movie
 * @property MovieRepositoryInterface $movieRepository
 */
class MovieService
{
    private MovieRepositoryInterface $movieRepository;

    public function __construct(MovieRepositoryInterface $movieRepository)
    {
        $this->movieRepository = $movieRepository;
    }

    /**
     * Метод поиска фильма по его id в базе данных OMDB (https://www.omdbapi.com/)
     * @param  string $imdbId - id фильма в базе данных OMDB
     *
     * @return array|null - массив с информацией о фильме (в последствии будет заменён на модель класса Movie), полученный из базы данных OMDB через конкретную реализацию интерфейса репозитория MovieRepositoryInterface
     */
    public function searchMovie(string $imdbId): ?array // TODO: Movie
    {
        return $this->movieRepository->findById($imdbId);
    }
}
