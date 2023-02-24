<?php
/** Задание 1.9. Поставщик данных
* Набор классов, которые будут использоваться для запроса информации о фильме из сервиса http://omdbapi.com.
* На вход основному методу для поиска фильма передаётся его IMDB ID.
* В качестве результата необходимо вернуть массив с информацией о фильме.
*/

// Класс модели фильм
class Movie
{
    private array $moviInfo = [
        'imdbID', // Идентификатор фильма IMDb в OMDb (например, tt1285016)
        'title', // Название фильма
        'poster', // Афиша фильма
        'releaseYear', // Год выпуска
        'duration', // Продолжительность фильма
        'genres', // Жанры фильма
        'director', // Режиссёр фильма
        'actors' // Актёры фильма
    ];

    /**
     * Метод возврата информации о фильме
     * 
     * @return array - массив с информацией о фильме
     */
    public function getMovieInfo(): array
    {
        return $this->movieInfo;
    }
}

// Интерфейс репозитория для модели класса Movi
interface MoviRepositoryInterface
{
    public function getMovie(int $imdbId);
}
