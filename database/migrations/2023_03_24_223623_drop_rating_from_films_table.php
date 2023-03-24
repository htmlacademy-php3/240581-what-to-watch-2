<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropRatingFromFilmsTable extends Migration
{
    /**
     * Удаление столбца "rating" (рейтинг фильма) из таблицы films
     *
     * @return void
     */
    public function up()
    {
        Schema::table('films', function (Blueprint $table) {
            $table->dropColumn('rating');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('films', function (Blueprint $table) {
            $table->unsignedInteger('rating')->nullable()->comment('Рейтинг фильма, в виде числа, количество голосов');
        });
    }
}
