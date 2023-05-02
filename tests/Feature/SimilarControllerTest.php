<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use \App\Models\Film;
use \App\Models\FilmGenre;
use \App\Models\Genre;

class SimilarControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест action index() SimilarController`а
     *
     * @return void
     */
    public function test_index()
    {
        Film::factory(30)->create();
        Genre::factory(10)->create();

        // У каждого фильма должен быть жанр
        foreach (Film::all() as $film) {
            foreach (Genre::all()->random(mt_rand(1, 3)) as $randomGenre) {
                FilmGenre::factory()->state([
                    'film_id' => $film->id,
                    'genre_id' => $randomGenre->id,
                ])->create();
            }
        }

        $similarCount = 4;

        $film = Film::orderByRaw("RAND()")->first();

        $response = $this->getJson("/api/films/{$film->id}/similar");

        $response
            ->assertOk()
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'name',
                    'poster_image',
                    'preview_video_link'
                ]
            ])
            // Проверка, что возвращено 4 фильма (пагинация), текущая страница "1" и найдены все фильмы
            ->assertJsonCount($similarCount, '*');

        // Проверка, что полученные фильмы имеют тот же жанр
        $responseData = $response->json();

        $similarGenreIds = [];
        $filmGenreIds = [];

        foreach ($responseData as $element) {
            $similar = Film::find($element['id']);

            foreach ($similar->genres as $genre) {
                $similarGenreIds[] = $genre->id;
            }
        }

        foreach ($film->genres as $filmGenre) {
            $filmGenreIds[] = $filmGenre->id;
        }

        assert((bool) array_intersect($similarGenreIds, $filmGenreIds));
    }
}
