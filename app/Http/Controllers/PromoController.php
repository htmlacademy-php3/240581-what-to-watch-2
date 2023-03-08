<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        //
    }

    /**
     * Установка промо-фильма.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        //
    }

    /**
     * Снятие установки промо- с фильма.(Удаление его из списка Promo)
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
