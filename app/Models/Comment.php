<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class Comment extends Model
{
    use HasFactory;

    /**
     * Таблица БД, ассоциированная с моделью.
     *
     * @var string
     */
    protected $table = 'comments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'text',
        'rating',
        'user_id',
        'film_id',
        'comment_id',
    ];

    /**
     * Получение фильма, к которому принадлежит комментарий.
     *
     * @return BelongsTo
     */
    public function film(): BelongsTo
    {
        return $this->belongsTo(Film::class, 'film_id', 'id');
    }

    /**
     * Получение пользователя, оставившего комментарий.
     * Комментарии, загруженные из внешнего источника, не имеют связи с пользователем сайта.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)
            ->withDefault([
                'name' => 'Гость',
            ]);
    }

    /*
    public function newCollection(array $models = [])
    {
        return new CommentCollection($models);
    }
    */
    /**
     * Метод получения дочерних комментариев комментария с сортировкой
     *
     * @return Collection
     */
    public function getThreadedComments(): Collection
    {
        return Comment::where('comment_id', $this->id)->get()->sortByDesc('created_at');
    }
}
