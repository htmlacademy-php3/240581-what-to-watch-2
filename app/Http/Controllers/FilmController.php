<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Test;

class FilmController extends Controller
{
    /**
     * Получение списка фильмов.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->getResponse(Test::test());
    }

    /**
     * Добавление фильма в базу.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (/*Не модератор*/false) {
            return $this->getResponse([], Response::HTTP_FORBIDDEN);
        }
        return $this->getResponse(Test::test(), Response::HTTP_CREATED);
    }

    /**
     * Получение информации о фильме.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->getResponse(Test::test());
    }

    /**
     * Редактирование фильма.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (/*Не модератор*/false) {
            return $this->getResponse([], Response::HTTP_FORBIDDEN);
        }
        return $this->getResponse(Test::test(), Response::HTTP_NO_CONTENT);
    }
}
