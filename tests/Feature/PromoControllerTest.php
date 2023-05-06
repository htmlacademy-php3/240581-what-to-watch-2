<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use \App\Models\Film;
use \App\Models\User;
use Laravel\Sanctum\Sanctum;
//use function PHPUnit\Framework\assertEquals;

class PromoControllerTest extends TestCase
{
    use RefreshDatabase;

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

    /**
     * Тест action store() PromoController`а.
     *
     * @return void
     */
    public function test_store()
    {
        Film::factory(5)->state([
            'promo' => false,
        ])->create();

        Film::factory(1)->state([
            'promo' => true,
        ])->create();

        // Проверка, если пользователь неаутентифицирован
        $film = Film::where('promo', false)->orderByRaw("RAND()")->first();

        $response = $this->postJson("/api/promo/{$film->id}");

        $response->assertUnauthorized();

        // Проверка, если пользователь аутентифицирован, но не модератор
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->actingAs($user)->postJson("/api/promo/{$film->id}");

        $response->assertForbidden();

        // Проверка, если пользователь - модератор
        $moderator = Sanctum::actingAs(User::factory()->moderator()->create());

        $promo = Film::where('promo', true)->first();

        $response = $this->actingAs($moderator)->postJson("/api/promo/{$film->id}");

        $response->assertCreated();

        // Проверка, что фильм добавлен к Promo
        $newPromo = Film::find($film->id);

        $this->assertEquals($newPromo->promo, true);

        // Проверка, что бывший promo-фильм исключён из Promo
        $exPromo = Film::find($promo->id);
        $this->assertEquals($exPromo->promo, false);
    }
}
