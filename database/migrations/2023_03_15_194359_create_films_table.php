<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilmsTable extends Migration
{
    /**
     * Run the migrations.
     * Таблица хранения фильмов
     *
     * @return void
     */
    public function up()
    {
        Schema::create('films', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->comment('Название фильма');
            $table->string('poster_image', 255)->nullable()->comment('Большой постер');
            $table->string('preview_image', 255)->nullable()->comment('Превью (маленькое изображение)');
            $table->string('background_image', 255)->nullable()->comment('Обложка фильма');
            $table->char('background_color', 9)->nullable()->comment('Цвет фона для карточки фильма');
            $table->string('video_link', 255)->nullable()->comment('Ссылка на видео');
            $table->string('preview_video_link', 255)->nullable()->comment('Ссылка на превью видео');
            $table->string('description', 1000)->nullable()->comment('Описание фильма');
            $table->string('director', 255)->nullable()->comment('Режиссёр');
            $table->unsignedInteger('run_time')->nullable()->comment('Продолжительность фильма');
            $table->unsignedInteger('released')->nullable()->comment('Год выхода на экраны');
            $table->string('imdb_id')->unique()->comment('id фильма в The Open Movie Database');
            $table->set('status', ['pending', 'on moderation', 'ready'])->nullable()->comment('Статусы фильма в процессе добавления фильма в базу');
            $table->unsignedInteger('rating')->nullable()->comment('Рейтинг фильма, в виде числа, количество голосов');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('films');
    }
}
