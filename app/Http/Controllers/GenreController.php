<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;

class GenreController extends Controller
{
    /**
     * Получение списка жанров.
     *
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function index(): ApiSuccessResponse|ApiErrorResponse
    {
        return new ApiSuccessResponse();
    }

    /**
     * Добавление жанра в базу.
     * !!!В ТЗ не указано, но логично, что список жанров скорее всего может меняться.
     * !!!Но, если что, удалю.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function store(Request $request): ApiSuccessResponse|ApiErrorResponse
    {
        return new ApiSuccessResponse([], Response::HTTP_CREATED);
    }

    /**
     * Редактирование жанра.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function update(Request $request, $id): ApiSuccessResponse|ApiErrorResponse
    {
        return new ApiSuccessResponse([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Удаление жанра.
     * !!!В ТЗ не указано, но логично, что список жанров скорее всего может меняться.
     * !!!Но, если что, удалю.
     *
     * @param  int  $id
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function destroy($id): ApiSuccessResponse|ApiErrorResponse
    {
        return new ApiErrorResponse([], Response::HTTP_NOT_FOUND);
    }
}
