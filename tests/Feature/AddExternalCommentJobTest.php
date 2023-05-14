<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use App\Models\Film;
use App\Jobs\AddExternalCommentJob;

class AddExternalCommentJobTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Проверка метода handle класса AddFilmJob по добавлению фильма в базу
     *
     * @return void
     */
    public function test_adding_a_new_comment_to_the_database(): void
    {
        $film = Film::factory()->create();

        $newCommentData = [
            'text' => $this->faker->paragraph(),
            'film_id' => $film->id,
            'date' => $this->faker->dateTimeBetween('-1 day'),
        ];

        $addExternalCommentJob = new AddExternalCommentJob($film, $newCommentData);
        $addExternalCommentJob->handle();

        // Проверка, что в базе данных появились записи: 1 фильма, 3-х актёров, 2-х жанров
        $this->assertDatabaseCount('comments', 1);
        $this->assertDatabaseHas('comments', [
            'user_id' => null,
            'film_id' => $film->id,
            'text' => $newCommentData['text'],
            'created_at' => $newCommentData['date'],
        ]);
    }

    /**
     * Проверка добавления задачи в очередь
     *
     * @return void
     */
    public function test_for_adding_a_task_to_the_queue(): void
    {
        Queue::fake();

        $film = Film::factory()->create();

        $newComment = [
            'text' => $this->faker->paragraph(),
            'date' => date("Y-m-d H:i:s"),
            'imdb_id' => $film->imdb_id,
        ];

        AddExternalCommentJob::dispatch($film, $newComment);

        Queue::assertPushed(AddExternalCommentJob::class);
    }
}
