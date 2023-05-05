<?php

namespace App\Services;

use App\Models\Comment;
use App\Http\Resources\CommentResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\AddCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
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
     * Обновление комментария.
     *
     * @param  UpdateCommentRequest $request
     *
     * @return void
     */
    public function updateComment(UpdateCommentRequest $request, Comment $сomment): void
    {
        $сomment->text = $request->text;

        if (isset($request->rating)) {
            $сomment->rating = $request->rating;
        }

        if ($сomment->isDirty()) {
            $сomment->save();
        }
    }

    /**
     * Метод получения комментариев к родительскому комментарию.
     *
     * @param  int $id - id родительского комментария
     *
     * @return Collection
     */
    public static function getThreadedComments($id): AnonymousResourceCollection
    {
        $threadedComments = Comment::where('comment_id', $id)->with('user')->get()->groupBy('comment_id')->sortByDesc('created_at');

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

        if (isset($request->comment_id)) {
            $comment->comment_id = $request->comment_id;
        }

        $comment->save();

        $newCommentResource = new CommentResource($comment);

        return $newCommentResource->toArray($comment);
    }
}
