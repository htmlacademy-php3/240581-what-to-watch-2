<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Test;

class PromoController extends Controller
{
    /**
     * Получение промо-фильма.
     * Согласно ТЗ промо-фильм один, но это не всегда может быть так.
     * В любом случае, один фильм - частный случай списка фильмов.
     * Также возможна ситуация отсутствия промо-фильма в какой-то момент времени.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->getResponse(Test::test());
    }

    /**
     * Установка промо-фильма.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        if (/*Не модератор*/false) {
            return $this->getResponse([], Response::HTTP_FORBIDDEN);
        }
        return $this->getResponse(Test::test(), Response::HTTP_CREATED);
    }

    /**
     * Снятие установки промо- с фильма.(Удаление его из списка Promo)
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->getResponse(Test::test(), Response::HTTP_NO_CONTENT);
    }
}
