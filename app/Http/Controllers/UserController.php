<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\AbstractApiResponse;

class UserController extends Controller
{
    /**
     * Получение списка пользователей.
     * !!! В ТЗ не указано, но наверняка нажно для модератора
     *
     * @return AbstractApiResponse
     */
    public function index(): AbstractApiResponse
    {
        return new ApiSuccessResponse();
    }

    /**
     * Получение профиля пользователя.
     *
     * @param  int  $id
     * @return AbstractApiResponse
     */
    public function show($id): AbstractApiResponse
    {
        return new ApiErrorResponse([], Response::HTTP_NOT_FOUND);
    }

    /**
     * Обновление профиля пользователя.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return AbstractApiResponse
     */
    public function update(Request $request, $id): AbstractApiResponse
    {
        return new ApiSuccessResponse();
    }

    /**
     * Удаление пользователя из базы.
     * !!! В ТЗ не предусмотрено, но на всякий случай зарезервировал
     *
     * @param  int  $id
     * @return AbstractApiResponse
     */
    public function destroy($id): AbstractApiResponse
    {
        return new ApiSuccessResponse([], Response::HTTP_NO_CONTENT);
    }
}
