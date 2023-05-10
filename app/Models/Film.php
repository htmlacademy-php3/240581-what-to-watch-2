<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Film extends Model
{
    use HasFactory;

    public const FILM_STATUS_MAP = [
        'pending' => 'pending',
        'on moderation' => 'on moderation',
        'ready' => 'ready',
    ];

    /**
     * Таблица БД, ассоциированная с моделью.
     *
     * @var string
     */
    protected $table = 'films';

    protected $hidden = array('pivot');

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'poster_image',
        'preview_image',
        'background_image',
        'background_color',
        'description',
        'director',
        'run_time',
        'released',
        'imdb_id',
        'status',
        'video_link',
        'preview_video_link',
    ];

    /**
     * Получение пользователей, добавивших этот фильм в "Избранное".
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites', 'film_id', 'user_id');
    }

    /**
     * Получение жанров, к которым относится фильм.
     *
     * @return BelongsToMany
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'films_genres', 'film_id', 'genre_id')->withTimestamps();
    }

    /**
     * Получение актёров, снимавшихся в фильме.
     *
     * @return BelongsToMany
     */
    public function actors(): BelongsToMany
    {
        return $this->belongsToMany(Actor::class, 'films_actors', 'film_id', 'actor_id')->withTimestamps();
    }

    /**
     * Получение комментариев, добавленных к фильму.
     *
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Метод получения рейтинга фильма по количеству голосов (отзывов) пользователей.
     *
     * @return int Количество голосов (отзывов), оставленных пользователями к фильму
     */
    public function getRating(): int
    {
        return $this->comments->count('rating');
    }

    /**
     * Метод получения рейтинга фильма по оценкам, оставленным пользователями.
     *
     * @return float Среднее арифметическое от оценок, оставленных пользователями
     */
    public function getTotalRating(): float
    {
        return round($this->comments->avg('rating'), 1);
    }
}
