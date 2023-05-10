<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use \App\Models\Actor;
use \App\Models\Comment;
use \App\Models\Favorite;
use \App\Models\Film;
use \App\Models\Genre;
use \App\Models\User;

class ModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест модели класса Actor.
     *
     * @return void
     */
    public function test_actor(): void
    {
        // Создание Actor factory
        $actor = Actor::factory()->create();

        // Проверка на наличие и соответствие классу Actor
        $this->assertInstanceOf(Actor::class, $actor);
    }

    /**
     * Тест модели класса Film.
     *
     * @return void
     */
    public function test_film(): void
    {
        // Создание Film factory
        $film = Film::factory()->create();

        // Проверка на наличие и соответствие классу Film созданного Film factory
        $this->assertInstanceOf(Film::class, $film);
    }

    /**
     * Тест модели класса Genre.
     *
     * @return void
     */
    public function test_genre(): void
    {
        // Создание Genre factory
        $genre = Genre::factory()->create();

        // Проверка на наличие и соответствие классу Film созданного Film factory
        $this->assertInstanceOf(Genre::class, $genre);
    }

    /**
     * Тест модели класса User.
     *
     * @return void
     */
    public function test_user(): void
    {
        // Создание User factory
        $user = User::factory()->create();

        // Проверка на наличие и соответствие классу User
        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * Тест модели класса Comment.
     *
     * @return void
     */
    public function test_comment(): void
    {
        // Создание фильма и пользователя
        $film = Film::factory()->create();
        $user = User::factory()->create();

        // Создание комментария пользователя к фильму
        $comment = Comment::factory()->create(['film_id' => $film->id, 'user_id' => $user->id]);

        // Проверка на наличие и соответствие классу Comment созданного Comment factory
        $this->assertInstanceOf(Comment::class, $comment);

        // Проверка на существование отношения комментарий-пользователь
        $this->assertNotEmpty($comment->user_id);
        $this->assertInstanceOf(User::class, $comment->user);

        // Проверка на существование отношения комментарий-фильм
        $this->assertNotEmpty($comment->film_id);
        $this->assertInstanceOf(Film::class, $comment->film);

        // Проверка, что новый экземпляр Comment::class имеет film_id и user_id как у объектов, участвовавших в его создании.
        $this->assertEquals($user->id, $comment->user_id);
        $this->assertEquals($film->id, $comment->film_id);
    }

    /**
     * Тест модели класса Comment.
     *
     * @return void
     */
    public function test_favorite(): void
    {
        // Создание фильма и пользователя
        $film = Film::factory()->create();
        $user = User::factory()->create();

        // Создание объекта класса Favorite
        $favorite = Favorite::factory()->create(['film_id' => $film->id, 'user_id' => $user->id]);

        // Проверка на наличие и соответствие классу Favorite созданного Favorite factory
        $this->assertInstanceOf(Favorite::class, $favorite);

        // Проверка на существование отношения избранное-пользователь
        $this->assertNotEmpty($favorite->user_id);
        $this->assertInstanceOf(User::class, $favorite->user);

        // Проверка, что новый экземпляр Favorite::class имеет film_id и user_id как у объектов, участвовавших в его создании.
        $this->assertEquals($user->id, $favorite->user_id);
        $this->assertEquals($film->id, $favorite->film_id);
    }
}
