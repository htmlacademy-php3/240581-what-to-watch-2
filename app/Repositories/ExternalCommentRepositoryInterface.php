<?php

namespace App\Repositories;

/**
 * Интерфейс репозитория для класса Comment
 *
 */
interface ExternalCommentRepositoryInterface
{
    public function findAllNew();
}
