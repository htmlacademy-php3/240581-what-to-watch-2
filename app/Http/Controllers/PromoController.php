<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\AbstractApiResponse;

class PromoController extends Controller
{
    /**
     * Получение промо-фильма.
     *
     * @return AbstractApiResponse
     */
    public function index(): AbstractApiResponse
    {
        return new ApiErrorResponse([], Response::HTTP_NOT_FOUND);
    }

    /**
     * Установка промо-фильма.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return AbstractApiResponse
     */
    public function store(Request $request, $id): AbstractApiResponse
    {
        return new ApiSuccessResponse([], Response::HTTP_CREATED);
    }

    /**
     * Снятие установки промо- с фильма.(Удаление его из списка Promo)
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return new ApiSuccessResponse([], Response::HTTP_NO_CONTENT);
    }
}
