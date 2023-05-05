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
     *
     * @return void
     */
    public function updateGenre(UpdateGenreRequest $request): void
    {
        $this->genre->title = $request->title;

        if ($this->genre->isDirty()) {
            $this->genre->save();
        }
    }
}
