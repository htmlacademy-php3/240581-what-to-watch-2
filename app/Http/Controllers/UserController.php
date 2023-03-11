<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Test;

class UserController extends Controller
{
    /**
     * Получение списка пользователей.
     * !!! В ТЗ не указано, но наверняка нажно для модератора
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (/*Не авторизован или не владелец ресурса или не модератор*/false) {
            return $this->getResponse([], Response::HTTP_FORBIDDEN);
        }
        return $this->getResponse(Test::test());
    }

    /**
     * Получение профиля пользователя.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (/*Не авторизован или не владелец ресурса или не модератор*/false) {
            return $this->getResponse([], Response::HTTP_FORBIDDEN);
        }
        return $this->getResponse(Test::test());
    }

    /**
     * Обновление профиля пользователя.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (/*Не авторизован или не владелец ресурса или не модератор*/false) {
            return $this->getResponse([], Response::HTTP_FORBIDDEN);
        }
        return $this->getResponse(Test::test());
    }

    /**
     * Удаление пользователя из базы.
     * !!! В ТЗ не предусмотрено, но на всякий случай зарезервировал
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (/*Не авторизован или не владелец ресурса или не модератор*/false) {
            return $this->getResponse([], Response::HTTP_FORBIDDEN);
        }
        return $this->getResponse(Test::test(), Response::HTTP_NO_CONTENT);
    }
}
