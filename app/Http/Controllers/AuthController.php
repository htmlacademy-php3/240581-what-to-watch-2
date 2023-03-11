<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Test;

class AuthController extends Controller
{
    /**
     * Аутентификация пользователя
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if (/*Авторизация*/false) {
            return $this->getResponse(Test::test(), Response::HTTP_UNAUTHORIZED);
        }
        return $this->getResponse(Test::test());
    }

    /**
     * Выход пользователяиз системы
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        return $this->getResponse(Test::test(), Response::HTTP_NO_CONTENT);
    }

    /**
     * Регистрация пользователя
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        return $this->getResponse(Test::test(), Response::HTTP_CREATED);
    }
}
