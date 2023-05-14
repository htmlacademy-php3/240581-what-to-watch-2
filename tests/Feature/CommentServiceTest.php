<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use App\Jobs\AddExternalCommentJob;
use App\Models\Film;
use App\Services\CommentService;

class CommentServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Проверка работы метода attachNewCommentToFilm() CommentService`а.
     *
     * @return void
     */
    public function test_attach_new_comment_to_film(): void
    {
        Queue::fake();

        $commentService = new CommentService();

        // Проверка, что в очередь не добавляются задачи для фильмов, которых в БД нет
        $unnecessaryComments[] = [
            'text' => $this->faker->paragraph(),
            'date' => date("Y-m-d H:i:s"),
            'imdb_id' => 'tt' . $this->faker->unique()->randomNumber(7, true),
        ];

        $commentService->attachNewCommentToFilm($unnecessaryComments);
        Queue::assertNotPushed(AddExternalCommentJob::class);

        // Проверка, что в очередь добавляются задачи для имеющихся в БД фильмов
        $films = Film::factory(6)->create();

        $newComments = [];

        foreach ($films as $film) {
            $newComment = [
                'text' => $this->faker->paragraph(),
                'date' => date("Y-m-d H:i:s"),
                'imdb_id' => $film->imdb_id,
            ];
            $newComments[] = $newComment;
        }

        $commentService->attachNewCommentToFilm($newComments);

        Queue::assertPushed(AddExternalCommentJob::class);
    }
}
