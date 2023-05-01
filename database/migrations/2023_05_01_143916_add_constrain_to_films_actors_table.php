<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConstrainToFilmsActorsTable extends Migration
{
    /**
     * Run the migrations.
     * Ограничение на уникальность пары film_id-actor_id в таблице films_actors
     *
     * @return void
     */
    public function up()
    {
        Schema::table('films_actors', function (Blueprint $table) {
            $table->unique(['film_id', 'actor_id'], 'film_id_actor_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('films_actors', function (Blueprint $table) {
            $table->dropUnique(['film_id', 'actor_id'], 'film_id_actor_id_unique');
        });
    }
}
