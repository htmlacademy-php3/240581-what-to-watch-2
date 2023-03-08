<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GenreController extends Controller
{
    /**
     * Получение списка жанров.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
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
        //
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
        //
    }
}
