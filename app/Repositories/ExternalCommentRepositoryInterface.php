<?php

namespace App\repositories;

/**
 * Интерфейс репозитория для класса Comment
 *
 */
interface ExternalCommentRepositoryInterface
{
    public function findAllNew();
}
