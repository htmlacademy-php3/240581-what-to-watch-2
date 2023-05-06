<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use \App\Models\Comment;
use App\Models\Film;
use App\Http\Resources\CommentResource;
use App\Http\Requests\AddCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\services\CommentService;

class CommentController extends Controller
{
    /**
     * Получение списка отзывов к фильму.
     *
     * @param  int $id - id фильма
     *
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function index(int $id): ApiSuccessResponse|ApiErrorResponse
    {
        $film = Film::findOrFail($id);

        $comments = $film->comments->where('comment_id', null)->sortByDesc('created_at');

        $commentsCollection = CommentResource::collection($comments)->toArray($comments);

        return new ApiSuccessResponse($commentsCollection);
    }


    /**
     * Добавление отзыва к фильму.
     *
     * @param  AddCommentRequest  $request
     * @param  int $id - id комментируемого фильма
     *
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function store(AddCommentRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        // На случай, если фильма с таким id в БД нет
        Film::findOrFail($request->id);

        $newComment = CommentService::createComment($request);

        return new ApiSuccessResponse($newComment, Response::HTTP_CREATED);
    }

    /**
     * Редактирование отзыва к фильму.
     * Доступно только автору отзыва и модератору
     *
     * @param  Request  $request
     * @param  int  $id - id отзыва
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function update(UpdateCommentRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        $comment = Comment::findOrFail($request->comment);

        $this->authorize('update', $comment);

        $commentService = new CommentService();
        $commentService->updateComment($request, $comment);

        return new ApiSuccessResponse([], Response::HTTP_ACCEPTED);
    }

    /**
     * Удаление отзыва к фильму.
     * Доступно только автору отзыва и модератору
     *
     * @param  int  $id - id отзыва
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function destroy(int $id): ApiSuccessResponse|ApiErrorResponse
    {
        $comment = Comment::findOrFail($id);

        $this->authorize('delete', $comment);

        $commentService = new CommentService();

        $responseCode = $commentService->deleteComment($comment);

        if (Response::HTTP_INTERNAL_SERVER_ERROR === $responseCode) {
            return new ApiErrorResponse([], Response::HTTP_INTERNAL_SERVER_ERROR, 'Комментарий удалить не удалось. Попробуйте позже!');
        }

        if (Response::HTTP_FORBIDDEN === $responseCode) {
            return new ApiErrorResponse([], Response::HTTP_FORBIDDEN, 'Комментарий удалить невозможно');
        }

        return new ApiSuccessResponse([], Response::HTTP_NO_CONTENT);
    }
}
