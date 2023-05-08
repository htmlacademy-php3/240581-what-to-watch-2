<?php

namespace App\repositories;

//use Carbon\Carbon;

/**
 * Интерфейс репозитория для класса Comment
 *
 */
interface ExternalCommentRepositoryInterface
{
    public function findAllNew();
}
