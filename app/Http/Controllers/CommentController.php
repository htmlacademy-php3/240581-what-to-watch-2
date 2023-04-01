<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use \App\Models\Comment;
use App\services\PermissionCheckService;

class CommentController extends Controller
{
    /**
     * Получение списка отзывов к фильму.
     *
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function index(/* TO DO Film $Film */): ApiSuccessResponse|ApiErrorResponse
    {
        return new ApiSuccessResponse();
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
    public function update(Request $request,int $id/* TO DO , Film $Film */): ApiSuccessResponse|ApiErrorResponse
    {
        $comment = Comment::find($id);

        if (PermissionCheckService::checkPermission($comment)) {
            return new ApiSuccessResponse();
        }

        abort(Response::HTTP_FORBIDDEN, trans('auth.failed'));
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

        if (PermissionCheckService::checkPermission($comment)) {
            return new ApiSuccessResponse([], Response::HTTP_NO_CONTENT);
        }

        abort(Response::HTTP_FORBIDDEN, trans('auth.failed'));
    }
}
