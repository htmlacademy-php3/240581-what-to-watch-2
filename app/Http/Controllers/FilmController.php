<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\AbstractApiResponse;

class FilmController extends Controller
{
    /**
     * Получение списка фильмов.
     *
     * @return AbstractApiResponse
     */
    public function index(): AbstractApiResponse
    {
        return new ApiSuccessResponse();
    }

    /**
     * Добавление фильма в базу.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return AbstractApiResponse
     */
    public function store(Request $request): AbstractApiResponse
    {
        return new ApiSuccessResponse([], Response::HTTP_CREATED);
    }

    /**
     * Получение информации о фильме.
     *
     * @param  int  $id
     * @return AbstractApiResponse
     */
    public function show($id): AbstractApiResponse
    {
        $data = ['Error' => 'Error getting data.'];
        return new ApiErrorResponse($data);
    }

    /**
     * Редактирование фильма.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return AbstractApiResponse
     */
    public function update(Request $request, $id): AbstractApiResponse
    {
        return new ApiSuccessResponse([], Response::HTTP_NO_CONTENT);
    }
}
