<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\Sequence;
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
                    'parent_id' => $parentComment,
                ]
            ))
            ->create();

        $childOfChildComment = Comment::factory()
            ->state(new Sequence(
                fn ($sequence) => [
                    'film_id' => $referenceFilm,
                    'user_id' => $parentCommenter,
                    'parent_id' => $childComment,
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
            'parent_id' => null,
        ]);

        // Проверка добавления комментария на комментарий
        $commentator = Sanctum::actingAs(User::factory()->create());
        // dd($response->json());

        $reguestData['parent_id'] = $response->json()['id'];
        // dd([$reguestData, $response->json()['id']]);


        $response = $this->actingAs($commentator)->postJson("/api/comments/{$film->id}", $reguestData);

        //dd($response->json()['id']);
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
        assertEquals($commentOnComment->parent_id, $reguestData['parent_id']);

        //dd([$commentOnComment->parent_id, $reguestData['parent_id']]);
        /*
        $this->assertDatabaseHas('comments', [
            'id' => $response->json()['id'],
            'text' => $reguestData['text'],
            'rating' => $reguestData['rating'],
            'user_id' => $commentator->id,
            'film_id' => $film->id,
            'parent_id' => $reguestData['parent_id'],

        ]);*/
    }
}
