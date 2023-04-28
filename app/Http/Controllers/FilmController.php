<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Requests\AddFilmRequest;
use App\Models\Film;
use App\Http\Resources\FilmListResource;
use App\Jobs\AddFilmJob;
use App\services\FilmService;
use Illuminate\Support\Facades\Auth;

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

        AddFilmJob::dispatch($request->imdbId);

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
        $film = Film::find($id);
        // $favorite = Favorite::where('film_id', $film->id)->get();
        // $user = $favorite->user();
        // dd($film->users);
        return new ApiSuccessResponse();
    }

    /**
     * Редактирование фильма.
     *
     * @param  Request $request
     * @param  int $id
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function update(Request $request, int $id): ApiSuccessResponse|ApiErrorResponse
    {
        $this->authorize('update', Film::class);
        return new ApiSuccessResponse();
    }
}
