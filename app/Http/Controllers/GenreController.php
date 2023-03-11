<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Test;

class GenreController extends Controller
{
    /**
     * Получение списка жанров.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->getResponse(Test::test());
    }

    /**
     * Добавление жанра в базу.
     * !!!В ТЗ не указано, но логично, что список жанров скорее всего может меняться.
     * !!!Но, если что, удалю.
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
     * Редактирование жанра.
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

    /**
     * Удаление жанра.
     * !!!В ТЗ не указано, но логично, что список жанров скорее всего может меняться.
     * !!!Но, если что, удалю.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (/*Не модератор*/false) {
            return $this->getResponse([], Response::HTTP_FORBIDDEN);
        }
        return $this->getResponse(Test::test(), Response::HTTP_NO_CONTENT);
    }
}
