<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;

class AuthController extends Controller
{
    /**
     * Аутентификация пользователя
     *
     * @param  \Illuminate\Http\Request  $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function login(Request $request): ApiSuccessResponse|ApiErrorResponse
    {
        return new ApiErrorResponse([], Response::HTTP_UNAUTHORIZED, 'Ошибка авторизации.');
    }

    /**
     * Выход пользователяиз системы
     *
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function logout(): ApiSuccessResponse|ApiErrorResponse
    {
        return new ApiSuccessResponse([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Регистрация пользователя
     *
     * @param  \Illuminate\Http\Request  $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function register(Request $request): ApiSuccessResponse|ApiErrorResponse
    {
        return new ApiSuccessResponse([], Response::HTTP_CREATED);
    }
}
