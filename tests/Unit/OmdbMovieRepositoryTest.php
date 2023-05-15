<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Repositories\OmdbMovieRepository;
use GuzzleHttp\Client;

class OmdbMovieRepositoryTest extends TestCase
{
    /**
     * Тест успешного ответа OmdbMovieRepository.
     *
     * @return void
     */
    public function test_success_find_film_by_id()
    {
        $httpClient = new Client();
        $repository = new OmdbMovieRepository($httpClient);
        $successDataKeys = [
            "name",
            "poster_image",
            "description",
            "director",
            "run_time",
            "released",
            "imdb_id",
            "actors",
            "genres",
        ];

        // Действительный идентификатор фильма
        $imdbId = 'tt0944947';

        $responseData = $repository->findById($imdbId);

        foreach ($successDataKeys as $key) {
            $this->assertArrayHasKey($key, $responseData);
        }
    }

    /**
     * Тест ответа OmdbMovieRepository, если фильм не найден
     * (был запрошен недействительный идентификатор фильма).
     *
     * @return void
     */
    public function test_not_found_find_film_by_id()
    {
        $httpClient = new Client();
        $repository = new OmdbMovieRepository($httpClient);
        $errorDataKeys = [
            "Response",
            "Error",
        ];

        // Недействительный идентификатор фильма
        $imdbId = 'tt6555577';

        $responseData = $repository->findById($imdbId);

        $this->assertEquals($responseData, null);
    }
}
