<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('films', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('poster_image')->nullable();
            $table->string('preview_image')->nullable();
            $table->string('cover')->nullable();
            $table->char('background_color', 7)->default('#000000');
            $table->year('released')->nullable();
            $table->text('description')->nullable();
            $table->string('director');
            $table->unsignedSmallInteger('run_time')->nullable();
            $table->string('video_link');
            $table->string('preview_video_link')->nullable();
            $table->unsignedMediumInteger('rating')->nullable();
            $table->string('imdb_id')->unique();
            $table->set('status', ['pending', 'on moderation', 'ready'])->nullable();
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
