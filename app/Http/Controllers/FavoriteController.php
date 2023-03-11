<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Test;

class FavoriteController extends Controller
{
    /**
     * Показ фильмов добавленных пользователем в избранное.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(/* TO DO User $User */)
    {
        if (/*Не владелец ресурса или не модератор*/false) {
            return $this->getResponse([], Response::HTTP_FORBIDDEN);
        }
        return $this->getResponse(Test::test());
    }

    /**
     * Добавление фильма в избранное.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request/* TO DO , User $User */)
    {
        if (/*Не владелец ресурса*/false) {
            return $this->getResponse([], Response::HTTP_FORBIDDEN);
        }
        if (/*Фильм уже в избранном*/false) {
            return $this->getResponse([], Response::HTTP_UNPROCESSABLE_ENTITY, 'Фильм уже находится в избранном');
        }
        return $this->getResponse(Test::test(), Response::HTTP_CREATED);
    }

    /**
     * Удаление фильма из избранного.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id/* TO DO , User $User */)
    {
        if (/*Не владелец ресурса*/false) {
            return $this->getResponse([], Response::HTTP_FORBIDDEN);
        }
        if (/*Фильм отсутствует в избранном*/false) {
            return $this->getResponse([], Response::HTTP_UNPROCESSABLE_ENTITY, 'Фильм отсутствует в списке избранного');
        }
        return $this->getResponse(Test::test(), Response::HTTP_NO_CONTENT);
    }
}
