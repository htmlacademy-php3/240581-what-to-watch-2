<?php

namespace App\repositories;


/**
 * Интерфейс репозитория для класса Movie
 *
 * @property string $id - id фильма
 */
interface MovieRepositoryInterface
{
   public function findById(string $id);
}
