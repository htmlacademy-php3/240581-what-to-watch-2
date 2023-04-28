<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;
use \App\Models\Actor;
use \App\Models\Comment;
use \App\Models\Favorite;
use \App\Models\Film;
use \App\Models\FilmActor;
use \App\Models\FilmGenre;
use \App\Models\Genre;
use \App\Models\User;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        Film::factory(50)->state(new Sequence(
            ['status' => Film::READY],
            ['status' => Film::READY],
            ['status' => Film::PENDING],
            ['status' => Film::READY],
            ['status' => Film::READY],
            ['status' => Film::READY],
            ['status' => Film::ON_MODERATION],
            ['status' => Film::READY],
            ['status' => Film::READY],
            ['status' => Film::READY],
        ))->create();

        Actor::factory(80)->create();
        User::factory(100)->create();
        Genre::factory(10)->create();

        $filmActorsActorIds = [];

        // У каждого фильма должны быть актёры
        foreach (Film::all() as $film) {
            // Количество актёров в фильме задано в диапазоне от 3 до 6
            for ($i = 0; $i < mt_rand(3, 6); $i++) {
                if ($i !== 0) {
                    $filmActorsActorIds[] = FilmActor::where('film_id', $film->id)->value('actor_id');
                }
                // В одном фильме актёр должен указываться не более одного раза
                $actor = Actor::whereNotIn('id', $filmActorsActorIds)->inRandomOrder()
                    ->first();

                FilmActor::factory()
                    ->state(new Sequence(
                        fn ($sequence) => ['film_id' => $film, 'actor_id' => $actor],
                    ))
                    ->create();
            }
        }

        // У каждого фильма должен быть жанр
        foreach (Film::all() as $film) {
            // Пусть жанров у фильма будет от 1 до 3-х
            for ($i = 0; $i < mt_rand(1, 3); $i++) {
                FilmGenre::factory()
                    ->state(new Sequence(
                        fn ($sequence) => ['film_id' => $film, 'genre_id' => Genre::all()->random()],
                    ))
                    ->create();
            }
        }

        Comment::factory()
            ->count(500)
            ->state(new Sequence(
                fn ($sequence) => ['film_id' => Film::all()->random(), 'user_id' => User::all()->random()],
            ))
            ->create();

        Favorite::factory()
            ->count(50)
            ->state(new Sequence(
                fn ($sequence) => ['film_id' => Film::all()->random(), 'user_id' => User::all()->random()],
            ))
            ->create();
    }
}
