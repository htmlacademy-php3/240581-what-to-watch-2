<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateGenreRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use App\Models\Genre;
use App\services\GenreService;

class GenreController extends Controller
{
    /**
     * Получение списка жанров.
     *
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function index(): ApiSuccessResponse|ApiErrorResponse
    {
        $genres = [
            'data' => Genre::all()
        ];

        return new ApiSuccessResponse($genres);
    }

    /**
     * Редактирование жанра.
     *
     * @param  UpdateGenreRequest $request
     *
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function update(UpdateGenreRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        $this->authorize('update', Genre::class);

        $genreService = new GenreService(Genre::findOrFail($request->id));

        $genreService->updateGenre($request);

        return new ApiSuccessResponse([], Response::HTTP_ACCEPTED);
    }
}
