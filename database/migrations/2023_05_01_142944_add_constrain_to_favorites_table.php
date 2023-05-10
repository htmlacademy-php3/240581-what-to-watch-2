<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConstrainToFavoritesTable extends Migration
{
    /**
     * Run the migrations.
     * Ограничение на уникальность пары user_id-film_id в таблице favorites
     *
     * @return void
     */
    public function up()
    {
        Schema::table('favorites', function (Blueprint $table) {
            $table->unique(['user_id', 'film_id'], 'user_id_film_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('favorites', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'film_id'], 'user_id_film_id_unique');
        });
    }
}
