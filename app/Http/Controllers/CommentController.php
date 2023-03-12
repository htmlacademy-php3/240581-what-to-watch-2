<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\AbstractApiResponse;

class CommentController extends Controller
{
    /**
     * Получение списка отзывов к фильму.
     *
     * @return AbstractApiResponse
     */
    public function index(/* TO DO Film $Film */): AbstractApiResponse
    {
        return new ApiSuccessResponse();
    }

    /**
     * Добавление отзыва к фильму.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return AbstractApiResponse
     */
    public function store(Request $request/* TO DO , Film $Film */): AbstractApiResponse
    {
        return new ApiErrorResponse();
    }

    /**
     * Редактирование отзыва к фильму.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return AbstractApiResponse
     */
    public function update(Request $request, $id/* TO DO , Film $Film */): AbstractApiResponse
    {
        return new ApiSuccessResponse();
    }

    /**
     * Удаление отзыва к фильму.
     *
     * @param  int  $id
     * @return AbstractApiResponse
     */
    public function destroy($id/* TO DO , Film $Film */): AbstractApiResponse
    {
        return new ApiErrorResponse([], Response::HTTP_FORBIDDEN, 'Комментарий удалить невозможно.');
    }
}
