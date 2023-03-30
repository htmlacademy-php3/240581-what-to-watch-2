<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\Sequence;
use \App\Models\Comment;
use \App\Models\Film;
use \App\Models\User;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Проверка, что у комментария есть специальное свойство для возврата имени автора и это свойство действительно содержит имя пользователя, который написал данный комментарий.
     *
     * @return void
     */
    public function test_to_get_comment_author_name(): void
    {
        $film = Film::factory()->create();
        $user = User::factory()->create();

        $comment = Comment::factory()
            ->state(new Sequence(
                fn ($sequence) => ['film_id' => $film, 'user_id' => $user],
            ))
            ->create();

        $autor = $comment->user;

        $this->assertEquals($user->name, $autor->name);
    }

    /**
     * Проверка, что для анонимных комментариев вместо имени автора выводиться дефолтный текст.
     *
     * @return void
     */
    public function test_with_anonymous_comment(): void
    {
        $film = Film::factory()->create();

        $comment = Comment::factory()
            ->state(new Sequence(
                fn ($sequence) => ['film_id' => $film, 'user_id' => null],
            ))
            ->create();

        $autor = $comment->user;

        $this->assertEquals('Гость', $autor->name);
    }
}
