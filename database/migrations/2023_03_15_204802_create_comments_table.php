<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     * Таблица хранения комментариев к фильмам
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade')->comment('id пользователя, оставившего комментарий. Комментарии, загруженные из внешнего источника, не имеют связи с пользователем сайта. Для них поле user_id оставляем пустым');
            $table->foreignId('film_id')->constrained('films')->onDelete('cascade')->comment('id комментируемого фильма');
            $table->unsignedInteger('comment_id')->nullable()->comment('id комментария, к которому оставлен этот комментарий (при наличии)');
            $table->string('text', 400)->comment('текст комментария');
            $table->tinyInteger('rating')->nullable()->comment('Оценка фильма');
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
        Schema::dropIfExists('comments');
    }
}
