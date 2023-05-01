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
        Film::factory(50)->hasActors(mt_rand(3, 6))->hasGenres(mt_rand(1, 3))->hasUsers(1)->state(new Sequence(
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

        Comment::factory(500)
            ->state(new Sequence(
                fn ($sequence) => ['film_id' => Film::all()->random(), 'user_id' => User::all()->random()],
            ))
            ->create();
    }
}
