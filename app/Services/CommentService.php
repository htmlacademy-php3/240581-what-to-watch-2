<?php

namespace App\Services;

use App\Models\Comment;
use App\Http\Resources\CommentResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Прикладной сервис для объектов класса Comment
 *
 * @param  Comment $comment - объект класса Comment
 *
 * @return array
 */
class CommentService
{
    /**
     * Метод получения комментариев к родительскому комментарию.
     *
     * @param  int $id - id родительского комментария
     *
     * @return Collection
     */
    public static function getThreadedComments($id): AnonymousResourceCollection
    {
        $threadedComments = Comment::where('parent_id', $id)->with('user')->get()->groupBy('parent_id')->sortByDesc('created_at');

        return CommentResource::collection($threadedComments);
    }
}
