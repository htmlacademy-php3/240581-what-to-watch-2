<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\AbstractApiResponse;

class FavoriteController extends Controller
{
    /**
     * Показ фильмов добавленных пользователем в избранное.
     *
     * @return AbstractApiResponse
     */
    public function index(/* TO DO User $User */): AbstractApiResponse
    {
        return new ApiSuccessResponse();
    }

    /**
     * Добавление фильма в избранное.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return AbstractApiResponse
     */
    public function store(Request $request/* TO DO , User $User */): AbstractApiResponse
    {
        return new ApiSuccessResponse([], Response::HTTP_CREATED);
    }

    /**
     * Удаление фильма из избранного.
     *
     * @param  int  $id
     * @return AbstractApiResponse
     */
    public function destroy($id/* TO DO , User $User */): AbstractApiResponse
    {
        return new ApiErrorResponse([], Response::HTTP_UNPROCESSABLE_ENTITY, 'Фильм отсутствует в списке избранного');
    }
}
