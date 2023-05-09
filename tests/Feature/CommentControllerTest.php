<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
    use RefreshDatabase, WithFaker;

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
            'text' => $this->faker->paragraph(),
            'rating' => $this->faker->numberBetween(1, 10),
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

        // Проверка добавления комментария на комментарий, если в запросе указан фильм, отличный от того, которому принадлежит родительский комментарий
        $anotherFilm = Film::factory()->create();
        $response = $this->actingAs($commentator)->postJson("/api/comments/{$anotherFilm->id}", $reguestData);

        $response->assertNotFound();
    }

    /**
     * Тест action update() CommentController`а.
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
                    'text' => $this->faker->paragraph(),
                    'rating' => $this->faker->numberBetween(1, 7),
                ],
            ))
            ->create();

        $commentData = [
            'text' => $this->faker->paragraph(),
            'rating' => $comment->rating + 1,
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
                    'text' => $this->faker->paragraph(),
                    'rating' => $this->faker->numberBetween(1, 10),
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
        $newText = $this->faker->paragraph();

        $response = $this->actingAs($user)->patchJson("/api/comments/{$comment->id}", ['text' => $newText]);
        $updatedComment = Comment::find($comment->id);
        $this->assertEquals($newText, $updatedComment->text);
        $this->assertEquals($commentData['rating'], $updatedComment->rating);
    }

    /**
     * Тест action destroy() CommentController`а.
     *
     * @return void
     */
    public function test_destroy()
    {
        $author = User::factory()->create();

        $anotherCommentator = User::factory()->create();

        $parentComment = Comment::factory()
            ->state(new Sequence(
                fn ($sequence) => [
                    'film_id' => Film::factory()->create(),
                    'user_id' => $author,
                ],
            ))
            ->create();

        $childComment = Comment::factory()
            ->state(new Sequence(
                fn ($sequence) => [
                    'film_id' => Film::factory()->create(),
                    'user_id' => $anotherCommentator,
                    'comment_id' => $parentComment->id,
                ],
            ))
            ->create();

        $commentChildChild = Comment::factory()
            ->state(new Sequence(
                fn ($sequence) => [
                    'film_id' => Film::factory()->create(),
                    'user_id' => $author,
                    'comment_id' => $childComment->id,
                ],
            ))
            ->create();

        $commentWithNoChildren = Comment::factory()
            ->state(new Sequence(
                fn ($sequence) => [
                    'film_id' => Film::factory()->create(),
                    'user_id' => $author,
                ],
            ))
            ->create();

        $someoneElseSComment = Comment::factory()
            ->state(new Sequence(
                fn ($sequence) => [
                    'film_id' => Film::factory()->create(),
                    'user_id' => $anotherCommentator,
                ],
            ))
            ->create();

        // Проверка, что комментарии существуют
        $this->assertModelExists($parentComment);
        $this->assertModelExists($childComment);
        $this->assertModelExists($commentChildChild);
        $this->assertModelExists($someoneElseSComment);

        // Проверка, если пользователь неаутентифицирован
        $response = $this->deleteJson("/api/comments/{$parentComment->id}");

        $response->assertUnauthorized();

        // Проверка, что комментарий не удалён
        $this->assertModelExists($parentComment);

        // Проверка, если пользователь аутентифицирован, но не модератор, а комментарий чужой
        $user = Sanctum::actingAs($author);

        $response = $this->actingAs($user)->deleteJson("/api/comments/{$someoneElseSComment->id}");

        $response->assertForbidden();

        // Проверка, что чужой комментарий не удалён
        $this->assertModelExists($someoneElseSComment);

        // Проверка, если пользователь аутентифицирован, но не модератор, комментарий его, но имеет потомков
        $user = Sanctum::actingAs($author);

        $response = $this->actingAs($user)->deleteJson("/api/comments/{$parentComment->id}");

        $response->assertForbidden();

        // Проверка, что комментарий, имеющий потомков не удалён
        $this->assertModelExists($parentComment);

        // Проверка, если пользователь аутентифицирован, но не модератор, комментарий его и без потомков
        $user = Sanctum::actingAs($author);

        $response = $this->actingAs($user)->deleteJson("/api/comments/{$commentWithNoChildren->id}");

        $response->assertNoContent();

        // Проверка, что комментарий без потомков удалён
        $this->assertModelMissing($commentWithNoChildren);

        // Проверка, если пользователь аутентифицирован как модератор, комментарий чужой и имеет потомков
        $moderator = Sanctum::actingAs(User::factory()->moderator()->create());

        $response = $this->actingAs($moderator)->deleteJson("/api/comments/{$parentComment->id}");

        $response->assertNoContent();

        // Проверка, что комментарий-родитель и все его потомки удалены
        $this->assertModelMissing($parentComment);
        $this->assertModelMissing($childComment);
        $this->assertModelMissing($commentChildChild);
    }
}
