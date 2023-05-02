<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\TestCase;
use Carbon\Carbon;
use \App\Models\Comment;
use \App\Models\Film;
use \App\Models\User;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест action index() CommentController`а.
     *
     * @return void
     */
    public function test_index()
    {
        // Проверка попытки обратиться к комментариям несуществующего фильма
        $filmId = 1;

        $response = $this->getJson("/api/comments/{$filmId}");

        $response
            ->assertNotFound();

        // Запустить `DatabaseSeeder`
        $this->seed();

        $film = Film::orderByRaw("RAND()")->first();

        $response = $this->getJson("/api/comments/{$film->id}");

        $response
            ->assertOk()
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'text',
                    'created_at',
                    'rating',
                    'author'
                ]
            ]);

        // Проверка сортировки отзывов от наиболее новых к старым (desc)
        $responseData = $response->json();

        $commentData = null;

        foreach ($responseData as $element) {
            $comment = Comment::find($element['id']);
            // dd($element);
            if ($commentData) {
                $parameter1 = $commentData;
                $parameter2 = $comment->created_at;

                assert($parameter1 >= $parameter2);
            }
            $commentData = $comment->created_at;
        }

        //Проверка, что в отсутствие id пользователя, оставившего комментарий, возвращается его имя как "Гость"

        Comment::factory()
            ->state(new Sequence(
                fn ($sequence) => ['film_id' => $film, 'user_id' => null,]
            ))
            ->create();

        $response = $this->getJson("/api/comments/{$film->id}");

        $response
            ->assertOk()
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'text',
                    'created_at',
                    'rating',
                    'author'
                ]
            ])
            ->assertJsonFragment([
                'author' => 'Гость'
            ]);
    }
}
