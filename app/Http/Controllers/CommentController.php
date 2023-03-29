<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;

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
     * @param  \Illuminate\Http\Request  $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function store(Request $request/* TO DO , Film $Film */): ApiSuccessResponse|ApiErrorResponse
    {
        return new ApiSuccessResponse();
    }

    /**
     * Редактирование отзыва к фильму.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function update(Request $request, $id/* TO DO , Film $Film */): ApiSuccessResponse|ApiErrorResponse
    {
        return new ApiSuccessResponse();
    }

    /**
     * Удаление отзыва к фильму.
     *
     * @param  int  $id
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function destroy($id/* TO DO , Film $Film */): ApiSuccessResponse|ApiErrorResponse
    {
        return new ApiSuccessResponse([], Response::HTTP_NO_CONTENT);
    }
}
