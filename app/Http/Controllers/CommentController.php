<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Test;

class CommentController extends Controller
{
    /**
     * Получение списка отзывов к фильму.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(/* TO DO Film $Film */)
    {
        return $this->getResponse(Test::test());
    }

    /**
     * Добавление отзыва к фильму.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request/* TO DO , Film $Film */)
    {
        if (/*Не авторизован*/false) {
            return $this->getResponse([], Response::HTTP_FORBIDDEN);
        }
        return $this->getResponse(Test::test(), Response::HTTP_CREATED);
    }

    /**
     * Редактирование отзыва к фильму.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id/* TO DO , Film $Film */)
    {
        if (/*Не владелец ресурса*/false) {
            return $this->getResponse([], Response::HTTP_FORBIDDEN);
        }
        return $this->getResponse(Test::test());
    }

    /**
     * Удаление отзыва к фильму.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id/* TO DO , Film $Film */)
    {
        if (/*(Владелец ресурса и у комментария нет ответов) или мдератор */false) {
            return $this->getResponse(Test::test(), Response::HTTP_NO_CONTENT);
        }
        return $this->getResponse([], Response::HTTP_FORBIDDEN, 'Комментарий удалить невозможно.');
    }
}
