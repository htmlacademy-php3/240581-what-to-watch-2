<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConstrainToFilmsGenresTable extends Migration
{
    /**
     * Run the migrations.
     * Ограничение на уникальность пары film_id-genre_id в таблице films_genres
     *
     * @return void
     */
    public function up()
    {
        Schema::table('films_genres', function (Blueprint $table) {
            $table->unique(['film_id', 'genre_id'], 'film_id_genre_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('films_genres', function (Blueprint $table) {
            $table->dropUnique(['film_id', 'genre_id'], 'film_id_genre_id_unique');
        });
    }
}
