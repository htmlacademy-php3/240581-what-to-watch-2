<?php

namespace App\Services;

use App\Models\Comment;
use App\Http\Resources\CommentResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use App\Http\Requests\AddCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

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
     * Метод удаления комментария со всеми потомками.
     *
     * @param  Comment $сomment
     *
     * @return int Код состояния HTTP
     */
    public function deleteComment(Comment $сomment): int
    {
        // Удаление комментария при отсутствии потомков.
        if (!self::getThreadedComments($сomment->id)->count()) {
            try {
                $сomment->delete();
                return Response::HTTP_NO_CONTENT;
            } catch (\Exception $exception) {
                return Response::HTTP_INTERNAL_SERVER_ERROR;
            }
        }

        // Удаление комментария при наличии у него потомков.
        if (Auth::user()->is_moderator && self::getThreadedComments($сomment->id)->count()) {

            $allChildIds = [];

            $childCommentsIds = $this->getTreeIdOfChildren($сomment->id, $allChildIds);

            $commentsCollection = $this->getAllChildIds($childCommentsIds);
            $commentsCollection->push($сomment->id);

            try {
                DB::beginTransaction();

                Comment::destroy($commentsCollection);

                DB::commit();
                return Response::HTTP_NO_CONTENT;
            } catch (\Exception $exception) {
                DB::rollBack();
                Log::warning($exception->getMessage());
                return Response::HTTP_INTERNAL_SERVER_ERROR;
            }
        }
        return Response::HTTP_FORBIDDEN;
    }

    /**
     * Метод получения id всех дочерних коментариев в виде многомерного массива.
     *
     * @param  int $сommentId - id родительского комментария
     * @param  array &$ids - массив для наполнения полученными результатами
     *
     * @return array $ids - массив, заполненый полученными результатами
     *
     */

    public function getTreeIdOfChildren(int $сommentId, array &$ids): array
    {
        $commentIds = Comment::select('id')->where('comment_id', $сommentId)->get()->toArray();
        $ids[] = $commentIds;

        if (count($commentIds)) {
            foreach ($commentIds as $id) {
                $this->getTreeIdOfChildren($id['id'], $ids);
            }
        }
        return $ids;
    }

    /**
     * Метод получения id всех дочерних коментариев в виде одномерной коллекции.
     *
     * @param  array $getTreeIdOfChildren - массив с id всех дочерних коментариев в виде многомерного массива
     *
     * @return Collection $ids - одномерная коллекция с id всех дочерних коментариев
     *
     */

    public function getAllChildIds(array $getTreeIdOfChildren): Collection
    {
        $commentsCollection = new Collection($getTreeIdOfChildren);

        return $commentsCollection->flatten();
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
