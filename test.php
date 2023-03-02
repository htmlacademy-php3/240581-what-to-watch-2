<?php

require_once('vendor/autoload.php');
require_once('OmdbMovieRepository.php');
require_once('MovieService.php');

use GuzzleHttp\Client;

// Тест 1. Поиск по действительному идентификатору фильма
$imdbId = 'tt0944947';

$httpClient = new Client();

$repository = new OmdbMovieRepository($httpClient);

$movieService = new MovieService($repository);

$movieInfo = $movieService->searchMovie($imdbId);

echo '<pre>';
var_dump($movieInfo);
echo '</pre>';

// Тест 2. Поиск по недействительному идентификатору фильма
$imdbId = 'tt6555577';

$httpClient = new Client();

$repository = new OmdbMovieRepository($httpClient);

$movieService = new MovieService($repository);

$movieInfo = $movieService->searchMovie($imdbId);

echo '<pre>';
var_dump($movieInfo);
echo '</pre>';
