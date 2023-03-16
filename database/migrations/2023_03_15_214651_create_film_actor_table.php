<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilmActorTable extends Migration
{
    /**
     * Run the migrations.
     * Промежуточная таблица для связи фильмов с участвовавшими в нём актёрами
     *
     * @return void
     */
    public function up()
    {
        Schema::create('film_actor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('film_id')->constrained()->onDelete('cascade')->comment('id фильма');
            $table->foreignId('actor_id')->constrained()->onDelete('cascade')->comment('id актёра, снимавшегося в фильме');
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
        Schema::dropIfExists('film_actor');
    }
}
