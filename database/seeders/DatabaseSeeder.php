<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;
use App\Models\Comment;
use App\Models\Favorite;
use App\Models\Film;
use App\Models\FilmGenre;
use App\Models\Genre;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        User::factory(15)->create();
        Genre::factory(10)->create();

        Film::factory(50)->hasActors(mt_rand(3, 6))->state(new Sequence(
            ['status' => Film::FILM_STATUS_MAP['ready']],
            ['status' => Film::FILM_STATUS_MAP['ready']],
            ['status' => Film::FILM_STATUS_MAP['pending']],
            ['status' => Film::FILM_STATUS_MAP['ready']],
            ['status' => Film::FILM_STATUS_MAP['ready']],
            ['status' => Film::FILM_STATUS_MAP['ready']],
            ['status' => Film::FILM_STATUS_MAP['on moderation']],
            ['status' => Film::FILM_STATUS_MAP['ready']],
            ['status' => Film::FILM_STATUS_MAP['ready']],
            ['status' => Film::FILM_STATUS_MAP['ready']],
        ))->create();

        // Зададим случайному количеству пользователей случайное количество избранных фильмов
        foreach (User::all()->random(mt_rand(6, 12)) as $user) {
            foreach (Film::all()->random(mt_rand(2, 7)) as $randomFilm) {
                Favorite::factory()->state([
                    'user_id' => $user->id,
                    'film_id' => $randomFilm->id,
                ])->create();
            }
        }

        // У каждого фильма должен быть жанр
        foreach (Film::all() as $film) {
            foreach (Genre::all()->random(mt_rand(1, 3)) as $randomGenre) {
                FilmGenre::factory()->state([
                    'film_id' => $film->id,
                    'genre_id' => $randomGenre->id,
                ])->create();
            }
        }

        Comment::factory(500)
            ->state(new Sequence(
                fn ($sequence) => ['film_id' => Film::all()->random(), 'user_id' => User::all()->random()],
            ))
            ->create();
    }
}
