<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;

class FilmController extends Controller
{
    /**
     * Получение списка фильмов.
     *
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function index(): ApiSuccessResponse|ApiErrorResponse
    {
        return new ApiSuccessResponse();
    }

    /**
     * Добавление фильма в базу.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function store(Request $request): ApiSuccessResponse|ApiErrorResponse
    {
        return new ApiSuccessResponse([], Response::HTTP_CREATED);
    }

    /**
     * Получение информации о фильме.
     *
     * @param  int  $id
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function show($id): ApiSuccessResponse|ApiErrorResponse
    {
        return new ApiSuccessResponse();
    }

    /**
     * Редактирование фильма.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function update(Request $request, $id): ApiSuccessResponse|ApiErrorResponse
    {
        return new ApiSuccessResponse([], Response::HTTP_NO_CONTENT);
    }
}
