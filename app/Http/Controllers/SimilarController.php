<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use App\Services\FilmService;

class SimilarController extends Controller
{
    /**
     * Получение списка похожих фильмов.
     *
     * @param  int $id - $id фильма
     *
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function index(int $id): ApiSuccessResponse|ApiErrorResponse
    {
        $fourFilmsCollection = FilmService::createRequestSimilarFilms($id);

        return new ApiSuccessResponse($fourFilmsCollection);
    }
}
