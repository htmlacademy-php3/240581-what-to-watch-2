<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Comment;
use App\Models\Film;

class AddExternalCommentJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Количество секунд, по истечении которых уникальная блокировка задания будет снята.
     *
     * @var int
     */
    public $uniqueFor = 3600;

    /**
     * Create a new job instance.
     *
     * @param  Film $film - фильм, к которому добавляется комментарий
     * @param  array $сomment - массив с данными комментария
     *
     * @return void
     */
    public function __construct(private Film $film, private array $сomment)
    {
    }

    /**
     * Создание нового комментария из полученных данных комментария из внешнего источника
     *
     * @return void
     */
    public function handle(): void
    {
        $newComment = new Comment([
            'text' => $this->сomment['text'],
            'film_id' => $this->film->id,
            'created_at' => $this->сomment['date'],
        ]);
        $newComment->save();
    }
}
