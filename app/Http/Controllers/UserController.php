<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;

class UserController extends Controller
{
    /**
     * Получение списка пользователей.
     * !!! В ТЗ не указано, но наверняка нажно для модератора
     *
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function index(): ApiSuccessResponse|ApiErrorResponse
    {
        return new ApiSuccessResponse();
    }

    /**
     * Получение профиля пользователя.
     *
     * @param  int  $id
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function show($id): ApiSuccessResponse|ApiErrorResponse
    {
        return new ApiErrorResponse([], Response::HTTP_NOT_FOUND);
    }

    /**
     * Обновление профиля пользователя.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function update(Request $request, $id): ApiSuccessResponse|ApiErrorResponse
    {
        return new ApiSuccessResponse();
    }

    /**
     * Удаление пользователя из базы.
     * !!! В ТЗ не предусмотрено, но на всякий случай зарезервировал
     *
     * @param  int  $id
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function destroy($id): ApiSuccessResponse|ApiErrorResponse
    {
        return new ApiSuccessResponse([], Response::HTTP_NO_CONTENT);
    }
}
