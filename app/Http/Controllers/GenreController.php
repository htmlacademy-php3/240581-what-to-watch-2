<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\AbstractApiResponse;

class GenreController extends Controller
{
    /**
     * Получение списка жанров.
     *
     * @return AbstractApiResponse
     */
    public function index(): AbstractApiResponse
    {
        return new ApiSuccessResponse();
    }

    /**
     * Добавление жанра в базу.
     * !!!В ТЗ не указано, но логично, что список жанров скорее всего может меняться.
     * !!!Но, если что, удалю.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return AbstractApiResponse
     */
    public function store(Request $request): AbstractApiResponse
    {
        return new ApiSuccessResponse([], Response::HTTP_CREATED);
    }

    /**
     * Редактирование жанра.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return AbstractApiResponse
     */
    public function update(Request $request, $id): AbstractApiResponse
    {
        return new ApiSuccessResponse([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Удаление жанра.
     * !!!В ТЗ не указано, но логично, что список жанров скорее всего может меняться.
     * !!!Но, если что, удалю.
     *
     * @param  int  $id
     * @return AbstractApiResponse
     */
    public function destroy($id): AbstractApiResponse
    {
        return new ApiErrorResponse([], Response::HTTP_NOT_FOUND);
    }
}
