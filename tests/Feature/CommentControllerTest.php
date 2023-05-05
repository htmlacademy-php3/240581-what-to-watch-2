<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use \App\Models\Comment;
use \App\Models\Film;
use \App\Models\User;
use Laravel\Sanctum\Sanctum;

use function PHPUnit\Framework\assertEquals;

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
                    'author',
                    'threadComments' => []
                ]
            ]);

        // Проверка сортировки отзывов от наиболее новых к старым (desc)
        $responseData = $response->json();

        $commentData = null;

        foreach ($responseData as $element) {
            $comment = Comment::find($element['id']);

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
                    'author',
                    'threadComments' => []
                ]
            ])
            ->assertJsonFragment([
                'author' => 'Гость'
            ]);

        //Проверка вложенности комментариев
        $referenceFilm = Film::factory()->create();
        $parentCommenter = User::orderByRaw("RAND()")->first();
        $childCommentAuthor = User::orderByRaw("RAND()")->first();
        $parentComment = Comment::factory()
            ->state(new Sequence(
                fn ($sequence) => [
                    'film_id' => $referenceFilm,
                    'user_id' => $parentCommenter,
                ]
            ))
            ->create();

        $childComment = Comment::factory()
            ->state(new Sequence(
                fn ($sequence) => [
                    'film_id' => $referenceFilm,
                    'user_id' => $childCommentAuthor,
                    'comment_id' => $parentComment,
                ]
            ))
            ->create();

        $childOfChildComment = Comment::factory()
            ->state(new Sequence(
                fn ($sequence) => [
                    'film_id' => $referenceFilm,
                    'user_id' => $parentCommenter,
                    'comment_id' => $childComment,
                ]
            ))
            ->create();

        $response = $this->getJson("/api/comments/{$referenceFilm->id}");

        $responseData = $response->json();

        $response
            ->assertOk()
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'text',
                    'created_at',
                    'rating',
                    'author',
                    'threadComments' => [
                        '*' => [
                            'id',
                            'text',
                            'created_at',
                            'rating',
                            'author',
                            'threadComments' => [
                                '*' => [
                                    'id',
                                    'text',
                                    'created_at',
                                    'rating',
                                    'author',
                                    'threadComments' => []
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
    }

    /**
     * Тест action store() CommentController`а.
     *
     * @return void
     */
    public function test_store()
    {
        $film = Film::factory()->create();

        $reguestData = [
            'text' => 'Consequatur nobis voluptas quam debitis nihil. Non laborum autem hic provident et nemo. Praesentium nam ut optio atque.',
            'rating' => 5,
        ];

        // Проверка, если пользователь неаутентифицирован
        $response = $this->postJson("/api/comments/{$film->id}", $reguestData);

        $response->assertUnauthorized();

        // Проверка, если пользователь аутентифицирован
        $user = Sanctum::actingAs(User::factory()->create());

        // Проверка попытки добавить комментарий к несуществующеу фильму
        $filmId = $film->id + 1;

        $response = $this->postJson("/api/comments/{$filmId}", $reguestData);

        $response->assertNotFound();

        // Проверка ответа при добавлении комментария
        $response = $this->actingAs($user)->postJson("/api/comments/{$film->id}", $reguestData);

        $response
            ->assertCreated()
            ->assertJsonStructure([
                'id',
                'text',
                'created_at',
                'rating',
                'author',
                'threadComments' => []
            ]);

        // Проверка сохранения комментария в БД
        $this->assertDatabaseCount('comments', 1);
        $this->assertDatabaseHas('comments', [
            'text' => $reguestData['text'],
            'rating' => $reguestData['rating'],
            'user_id' => $user->id,
            'film_id' => $film->id,
            'comment_id' => null,
        ]);

        // Проверка добавления комментария на комментарий
        $commentator = Sanctum::actingAs(User::factory()->create());

        $reguestData['comment_id'] = $response->json()['id'];

        $response = $this->actingAs($commentator)->postJson("/api/comments/{$film->id}", $reguestData);

        $response
            ->assertCreated()
            ->assertJsonStructure([
                'id',
                'text',
                'created_at',
                'rating',
                'author',
                'threadComments' => []
            ]);

        // Проверка сохранения комментария в БД
        $this->assertDatabaseCount('comments', 2);

        $commentOnComment = Comment::find($response->json()['id']);
        assertEquals($commentOnComment->text, $reguestData['text']);
        assertEquals($commentOnComment->rating, $reguestData['rating']);
        assertEquals($commentOnComment->user_id, $commentator->id);
        assertEquals($commentOnComment->comment_id, $reguestData['comment_id']);
    }

    /**
     * Тест action test_update() CommentController`а.
     *
     * @return void
     */
    public function test_update()
    {
        $comment = Comment::factory()
            ->state(new Sequence(
                fn ($sequence) => [
                    'film_id' => Film::factory()->create(),
                    'user_id' => User::factory()->create(),
                    'text' => 'Consequatur nobis voluptas quam debitis nihil. Non laborum autem hic provident et nemo. Praesentium nam ut optio atque.',
                    'rating' => 10,
                ],
            ))
            ->create();

        $commentData = [
            'text' => 'Modi cum perspiciatis minima nesciunt eveniet non deleniti. Qui ducimus deleniti excepturi. Minima et voluptatem in.',
            'rating' => 5,
        ];

        // Проверка, если пользователь неаутентифицирован
        $response = $this->patchJson("/api/comments/{$comment->id}", $commentData);

        $response->assertUnauthorized();

        // Проверка, если пользователь аутентифицирован, но не модератор, а комментарий чужой
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->actingAs($user)->patchJson("/api/comments/{$comment->id}", $commentData);

        $response->assertForbidden();

        // Проверка, если пользователь - модератор, а комментарий чужой
        $moderator = Sanctum::actingAs(User::factory()->moderator()->create());

        // а) Проверка работы при введении старых данных
        $response = $this->actingAs($moderator)->patchJson("/api/comments/{$comment->id}", [
            'text' => $comment->text,
            'rating' => $comment->rating
        ]);

        $response->assertStatus(Response::HTTP_ACCEPTED);

        // Проверка изменённых данных
        $updatedComment = Comment::find($comment->id);
        $this->assertEquals($comment->text, $updatedComment->text);
        $this->assertEquals($comment->rating, $updatedComment->rating);

        // б) Проверка работы при введении новых
        $response = $this->actingAs($moderator)->patchJson("/api/comments/{$comment->id}", $commentData);

        $response->assertStatus(Response::HTTP_ACCEPTED);

        // Проверка данных комментария на соответствие нововведённым
        $updatedComment = Comment::find($comment->id);
        $this->assertEquals($commentData['text'], $updatedComment->text);
        $this->assertEquals($commentData['rating'], $updatedComment->rating);

        // Проверка, если пользователь аутентифицирован, но не модератор, а комментарий свой
        $comment = Comment::factory()
            ->state(new Sequence(
                fn ($sequence) => [
                    'film_id' => Film::factory()->create(),
                    'user_id' => $user,
                    'text' => 'Numquam rerum dicta non aut omnis error. Aliquam tempora atque non sit aut itaque rerum sunt. Assumenda cumque eos voluptas maxime est.',
                    'rating' => 7,
                ],
            ))
            ->create();

        $response = $this->actingAs($user)->patchJson("/api/comments/{$comment->id}", $commentData);

        $response->assertStatus(Response::HTTP_ACCEPTED);

        // Проверка данных комментария на соответствие нововведённым
        $updatedComment = Comment::find($comment->id);
        $this->assertEquals($commentData['text'], $updatedComment->text);
        $this->assertEquals($commentData['rating'], $updatedComment->rating);

        // Проверка, если не выставлен необязательный при обновлении рейтинг
        $commentData['text'] = 'Repellendus animi in et. Ex quas nulla nihil at qui ea rerum. Quae ex aut rerum reiciendis delectus est animi ea. Soluta occaecati quo et totam voluptates neque.';

        $response = $this->actingAs($user)->patchJson("/api/comments/{$comment->id}", $commentData);
        $updatedComment = Comment::find($comment->id);
        $this->assertEquals($commentData['text'], $updatedComment->text);
        $this->assertEquals($commentData['rating'], $updatedComment->rating);
    }
}
