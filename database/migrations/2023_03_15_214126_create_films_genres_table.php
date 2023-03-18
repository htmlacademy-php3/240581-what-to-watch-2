<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilmsGenresTable extends Migration
{
    /**
     * Run the migrations.
     * Промежуточная таблица для связи фильмов с жанрами
     *
     * @return void
     */
    public function up()
    {
        Schema::create('films_genres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('film_id')->constrained()->onDelete('cascade')->comment('id фильма');
            $table->foreignId('genre_id')->constrained()->onDelete('cascade')->comment('id жанра, соответствующего фильму');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('films_genres');
    }
}
