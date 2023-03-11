<?php

namespace App\Models;

use App\Services\MovieService;
use App\Repositories\OmdbMovieRepository;
use GuzzleHttp\Client;
/**
 * Временная модель для тестирования запросов к The Open Movie Database.
 * В последствии будет удалена.
 *
 */
class Test
{
    public static function test()
    {
        // Действительный идентификатор фильма
        $imdbId = 'tt0944947';
        // Недействительный идентификатор фильма
        $imdbId = 'tt6555577';
        $httpClient = new Client();
        $repository = new OmdbMovieRepository($httpClient);
        $movieService = new MovieService($repository);

        return $movieService->searchMovie($imdbId);
    }
}
