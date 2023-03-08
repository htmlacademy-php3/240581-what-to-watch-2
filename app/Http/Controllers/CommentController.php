<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Получение списка отзывов к фильму.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(/* TO DO Film $Film */)
    {
        //
    }

    /**
     * Добавление отзыва к фильму.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request/* TO DO , Film $Film */)
    {
        //
    }

    /**
     * Редактирование отзыва к фильму.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id/*TO DO , Film $Film*/)
    {
        //
    }

    /**
     * Удаление отзыва к фильму.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id/* TO DO , Film $Film */)
    {
        //
    }
}
