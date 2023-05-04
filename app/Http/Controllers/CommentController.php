<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use \App\Models\Comment;
use App\Models\Film;
use App\Http\Resources\CommentResource;

class CommentController extends Controller
{
    /**
     * Получение списка отзывов к фильму.
     *
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function index(int $id): ApiSuccessResponse|ApiErrorResponse
    {
        $film = Film::findOrFail($id);

        $comments = $film->comments->where('parent_id', null)->sortByDesc('created_at');

        $commentsCollection = CommentResource::collection($comments)->toArray($comments);

        return new ApiSuccessResponse($commentsCollection);
    }


    /**
     * Добавление отзыва к фильму.
     *
     * @param  Request  $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function store(Request $request/* TO DO , Film $Film */): ApiSuccessResponse|ApiErrorResponse
    {
        return new ApiSuccessResponse([], Response::HTTP_CREATED);
    }

    /**
     * Редактирование отзыва к фильму.
     * Доступно только автору отзыва и модератору
     *
     * @param  Request  $request
     * @param  int  $id - id отзыва
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function update(Request $request, int $id/* TO DO , Film $Film */): ApiSuccessResponse|ApiErrorResponse
    {
        $comment = Comment::find($id);
        $this->authorize('update', $comment);
        return new ApiSuccessResponse();
    }

    /**
     * Удаление отзыва к фильму.
     * Доступно только автору отзыва и модератору
     *
     * @param  int  $id - id отзыва
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function destroy(int $id/* TO DO , Film $Film */): ApiSuccessResponse|ApiErrorResponse
    {
        $comment = Comment::find($id);
        $this->authorize('delete', $comment);
        return new ApiSuccessResponse([], Response::HTTP_NO_CONTENT);
    }
}
