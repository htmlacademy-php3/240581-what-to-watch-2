<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\AbstractApiResponse;

class AuthController extends Controller
{
    /**
     * Аутентификация пользователя
     *
     * @param  \Illuminate\Http\Request  $request
     * @return AbstractApiResponse
     */
    public function login(Request $request): AbstractApiResponse
    {
        return new ApiErrorResponse([], Response::HTTP_UNAUTHORIZED, 'Ошибка авторизации.');
    }

    /**
     * Выход пользователяиз системы
     *
     * @return AbstractApiResponse
     */
    public function logout(): AbstractApiResponse
    {
        return new ApiSuccessResponse([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Регистрация пользователя
     *
     * @param  \Illuminate\Http\Request  $request
     * @return AbstractApiResponse
     */
    public function register(Request $request): AbstractApiResponse
    {
        return new ApiSuccessResponse([], Response::HTTP_CREATED);
    }
}
