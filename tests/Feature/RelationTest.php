<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use \App\Models\Actor;
use \App\Models\Comment;
use \App\Models\Film;
use \App\Models\Genre;
use \App\Models\User;
use Database\Seeders\DatabaseSeeder;

class RelationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Проверка связей меду моделями.
     *
     * @return void
     */
    public function test_relation(): void
    {
        $databaseSeeder = new DatabaseSeeder;
        $databaseSeeder->run();

        $actor = Actor::all()->random();
        $film = Film::all()->random();
        $genre = Genre::all()->random();
        $user = User::all()->random();

        // Проверка на существование отношения актёр-фильмы
        $this->assertNotEmpty($actor->films);
        foreach ($actor->films as $film) {
            $this->assertInstanceOf(Film::class, $film);
        }

        // Проверка на существование отношения фильм-пользователи
        $this->assertNotEmpty($film->users);
        foreach ($film->users as $user) {
            $this->assertInstanceOf(User::class, $user);
        }

        // Проверка на существование отношения фильм-жанры
        $this->assertNotEmpty($film->genres);
        foreach ($film->genres as $genre) {
            $this->assertInstanceOf(Genre::class, $genre);
        }

        // Проверка на существование отношения фильм-актёры
        $this->assertNotEmpty($film->actors);
        foreach ($film->actors as $actor) {
            $this->assertInstanceOf(Actor::class, $actor);
        }

        // Проверка на существование отношения фильм-комментарии
        $this->assertNotEmpty($film->comments);
        foreach ($film->comments as $comment) {
            $this->assertInstanceOf(Comment::class, $comment);
        }

        // Проверка на существование отношения жанр-фильмы
        $this->assertNotEmpty($genre->films);
        foreach ($genre->films as $film) {
            $this->assertInstanceOf(Film::class, $film);
        }

        // Проверка на существование отношения пользователь-комментарии
        $this->assertNotEmpty($user->comments);
        foreach ($user->comments as $comment) {
            $this->assertInstanceOf(Comment::class, $comment);
        }

        // Проверка на существование отношения пользователь-фильмы
        $this->assertNotEmpty($user->films);
        foreach ($user->films as $film) {
            $this->assertInstanceOf(Film::class, $film);
        }
    }
}
