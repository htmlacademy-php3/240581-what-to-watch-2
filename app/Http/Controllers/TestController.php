<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\services\MovieService;
use App\repositories\OmdbMovieRepository;
use GuzzleHttp\Client;

class TestController extends Controller
{
    public function index()
    {
        // Тест 1. Поиск по действительному идентификатору фильма
        $imdbId = 'tt0944947';
        // Тест 2. Поиск по недействительному идентификатору фильма
        // $imdbId = 'tt6555577';

        $httpClient = new Client();

        $repository = new OmdbMovieRepository($httpClient);

        $movieService = new MovieService($repository);

        $movieInfo = $movieService->searchMovie($imdbId);

        if (!$movieInfo) {
            $movieInfo = 'По Вашему запросу ничего не найдено';
        }

        return $movieInfo;
    }
}
