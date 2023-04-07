<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Requests\AddFilmRequest;
use App\Models\Actor;
use App\Models\Film;
use App\Jobs\AddFilmJob;
use App\Services\FilmService;
use App\repositories\OmdbMovieRepository;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Bus;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class FilmController extends Controller
{
    /**
     * Получение списка фильмов.
     *
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function index(): ApiSuccessResponse|ApiErrorResponse
    {
        return new ApiSuccessResponse();
    }

    /**
     * Добавление фильма в базу.
     *
     * @param  Request  $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function store(AddFilmRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        $this->authorize('create', Film::class);

        return new ApiSuccessResponse([], Response::HTTP_CREATED);
    }

    /**
     * Получение информации о фильме.
     *
     * @param  int  $id
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function show(int $id): ApiSuccessResponse|ApiErrorResponse
    {
        return new ApiSuccessResponse();
    }

    /**
     * Редактирование фильма.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function update(Request $request, int $id): ApiSuccessResponse|ApiErrorResponse
    {
        $this->authorize('update', Film::class);
        return new ApiSuccessResponse();
    }
}
