<?php

namespace App\Services;

use App\Models\Genre;
use App\Http\Requests\UpdateGenreRequest;

/**
 * Прикладной сервис для объектов класса Genre
 *
 * @param  Genre $genre - объект класса Genre
 */
class GenreService
{
    public function __construct(
        private Genre $genre
    ) {
    }

    /**
     * Обновление жанра.
     *
     * @param  UpdateGenreRequest $request
     * @param  Genre $genre - модель класса Genre
     *
     * @return void
     */
    public function updateGenre(UpdateGenreRequest $request): void
    {
        $params = $request->toArray();

        $this->genre->title = $params['title'];

        if ($this->genre->isDirty()) {
            $this->genre->save();
        }
    }
}
