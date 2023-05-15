<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use App\Models\Film;
use App\Http\Resources\FilmResource;
use App\Services\FilmService;

class PromoController extends Controller
{
    /**
     * Получение промо-фильма.
     *
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function show(): ApiSuccessResponse|ApiErrorResponse
    {
        $promo = new FilmResource(Film::where('promo', true)->firstOrFail());
        $promoData = $promo->toArray($promo);

        return new ApiSuccessResponse($promoData);
    }

    /**
     * Установка промо-фильма.
     *
     * @param  Request  $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function store(Request $request): ApiSuccessResponse|ApiErrorResponse
    {
        $this->authorize('create', Film::class);

        $responseCode = FilmService::createPromo($request);

        if (Response::HTTP_INTERNAL_SERVER_ERROR === $responseCode) {
            return new ApiErrorResponse([], Response::HTTP_INTERNAL_SERVER_ERROR, 'Добавить фильм к Promo не удалось. Попробуйте позже!');
        }

        return new ApiSuccessResponse([], Response::HTTP_CREATED);
    }

    /**
     * Снятие установки промо- с фильма.(Удаление его из списка Promo)
     *
     * @param  int  $id
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function destroy(int $id): ApiSuccessResponse|ApiErrorResponse
    {
        return new ApiSuccessResponse([], Response::HTTP_NO_CONTENT);
    }
}
