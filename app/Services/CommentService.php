<?php

namespace App\Services;

use App\Models\Comment;
use App\Http\Resources\CommentResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\AddCommentRequest;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Метод создания модели класса Comment
     *
     * @param  AddCommentRequest $request - HTTP-запрос с данными нового комментария
     * @param  int $id - id комментируемого фильма
     *
     * @return array - массив с данными нового комментария
     */
    public static function createComment(AddCommentRequest $request): array
    {
        $comment = new Comment([
            'text' => $request->text,
            'rating' => $request->rating,
            'user_id' => Auth::id(),
            'film_id' => $request->id,
        ]);

        if (isset($request->parent_id)) {
            $comment->parent_id = $request->parent_id;
        }

        $comment->save();

        $newCommentResource = new CommentResource($comment);

        return $newCommentResource->toArray($comment);
    }
}
