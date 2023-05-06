<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use \App\Models\Film;
use \App\Models\User;

class PromoControllerTest extends TestCase
{
    /**
     * Тест action show() PromoController`а.
     *
     * @return void
     */
    public function test_show()
    {
        // Проверка на возврат 404 ошибки если к Promo не добавлен ни один фильм
        Film::factory(5)->create();

        $response = $this->getJson("/api/promo/");
        $response->assertNotFound();

        // Проверка получения promo-фильма
        $promo = Film::factory(1)->state([
            'promo' => true,
        ])->create();

        $response = $this->getJson("/api/promo/");

        $response
            ->assertOk()
            ->assertJsonStructure([
                'id',
                'name',
                'poster_image',
                'preview_image',
                'background_image',
                'background_color',
                'video_link',
                'preview_video_link',
                'description',
                'rating',
                'scores_count',
                'director',
                'starring' => [],
                'run_time',
                'genre' => [],
                'released',
                'is_favorite'
            ])
            ->assertJsonFragment(['is_favorite' => []])
            ->assertJsonMissing(['is_favorite' => false])
            ->assertJsonMissing(['is_favorite' => true]);
    }
}
