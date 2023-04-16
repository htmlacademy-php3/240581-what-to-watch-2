<?php

namespace App\Repositories;

/**
 * Интерфейс репозитория для класса Film
 *
 * @property string $id - id фильма
 */
interface MovieRepositoryInterface
{
    public function findById(string $id);
}
