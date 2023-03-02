<?php

/** 
 * Прикладной сервис MovieService,
 * используя MovieRepository осуществляет все операции с сущностью Movie
 * @property $movieInfo - массив с информацией о фильме
 * (в последствии будет заменён на модель класса Movie),
 * полученный из базы данных OMDB через конкретную реализацию интерфейса репозитория
 * MovieRepositoryInterface
 */
class MovieService
{
    private $movie;

    public function __construct(MovieRepositoryInterface $movie)
    {
        $this->movie = $movie;
    }

    /**
     * Метод поиска фильма по его id в базе данных OMDB (https://www.omdbapi.com/)
     * @param string $imdbId - id фильма в базе данных OMDB
     * 
     * @return array|null - массив с информацией о фильме (в последствии будет заменён на модель класса Movie), полученный из базы данных OMDB через конкретную реализацию интерфейса репозитория MovieRepositoryInterface
     */
    public function searchMovie(string $imdbId): ?array // TODO: Movie
    {
        return $this->movie->findById($imdbId);
    }
}
