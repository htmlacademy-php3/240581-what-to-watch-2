<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\Sequence;
use App\Models\Comment;
use App\Models\Film;

class FilmTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Проверка, что свойство getRating класса Film действительно возвращает правильный рейтинг, который основывается на оценках этого фильма, оставленных пользователями.
     * Рейтинг фильма расчитывается как среднее арифметическое от оценок пользователей
     *
     * @return void
     */
    public function test_movie_rating(): void
    {
        $film = Film::factory()->create();

        // Количество комментариев, а следовательно, т.к. оценка при отзыве обязательна, оценок.
        $referenceCountComment = 12;

        // Эталонные оценки
        $benchmarkRating1 = 3;
        $benchmarkRating2 = 7;
        $benchmarkRating3 = 9;

        // Рассчёт эталонного рейтинга фильма
        $sumbenchmarkRatings = ($benchmarkRating1 + $benchmarkRating2 + $benchmarkRating3) * $referenceCountComment / 3;
        $referenceRating = round($sumbenchmarkRatings / $referenceCountComment, 1);

        Comment::factory()
            ->count($referenceCountComment)
            ->state(new Sequence(
                // Эталонные оценки будут присваиваться поочерёдно.
                // Их общее количество будет равно $referenceCountComment
                // Во избежание ошибок $referenceCountComment лучше брать кратным количеству эталонных оценок.
                ['film_id' => $film, 'rating' => $benchmarkRating1],
                ['film_id' => $film, 'rating' => $benchmarkRating2],
                ['film_id' => $film, 'rating' => $benchmarkRating3],
            ))
            ->create();

        $comments = $film->comments;

        $rating = round($comments->avg('rating'), 1);

        $this->assertEquals($referenceRating, $rating);
    }

    /**
     * Проверка, что свойство getRating класса Film возвращает рейтинг, равный нулю, при отсутствии комментариев на фильм
     *
     * @return void
     */
    public function test_movie_rating_with_no_comments(): void
    {
        $film = Film::factory()->create();

        $comments = $film->comments;

        $rating = round($comments->avg('rating'), 1);

        $this->assertEquals(0, $rating);
    }
}
