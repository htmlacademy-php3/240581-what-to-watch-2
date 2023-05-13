<?php

namespace App\Repositories;

/**
 * Интерфейс репозитория для класса Film
 *
 * @property string $imdbId - id фильма
 */
interface MovieRepositoryInterface
{
    public function findById(string $imdbId);
}
