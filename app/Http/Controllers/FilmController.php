<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Requests\AddFilmRequest;
use App\Http\Requests\UpdateFilmRequest;
use App\Models\Film;
use App\Http\Resources\FilmListResource;
use App\Http\Resources\FilmResource;
use App\Jobs\AddFilmJob;
use App\services\FilmService;

class FilmController extends Controller
{
    /**
     * Получение списка фильмов.
     * @param  Request|null $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function index(Request $request): ApiSuccessResponse|ApiErrorResponse
    {
        $query = FilmService::createRequestForFilmsByParameters($request);
        $films = $query->paginate(8);

        $collection = FilmListResource::collection($films);

        return new ApiSuccessResponse($films);
    }

    /**
     * Добавление фильма в базу.
     *
     * @param  Request $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function store(AddFilmRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        $this->authorize('create', Film::class);

        AddFilmJob::dispatch($request->imdbId)->afterCommit();

        return new ApiSuccessResponse([], Response::HTTP_CREATED);
    }

    /**
     * Получение информации о фильме.
     *
     * @param  int $id
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function show(int $id): ApiSuccessResponse|ApiErrorResponse
    {
        $film = new FilmResource(Film::findOrFail($id));
        $film = $film->toArray($id);

        return new ApiSuccessResponse($film);
    }

    /**
     * Редактирование фильма.
     *
     * @param  UpdateFilmRequest $request
     *
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function update(UpdateFilmRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        $this->authorize('update', Film::class);

        $service = new FilmService();

        $service->updateFilm($request, Film::findOrFail($request->id));

        return new ApiSuccessResponse([], Response::HTTP_ACCEPTED);
    }
}
