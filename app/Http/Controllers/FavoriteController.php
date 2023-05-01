<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use App\Models\Favorite;
use App\Models\Film;
use App\Http\Resources\FilmListResource;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Показ фильмов добавленных пользователем в избранное.
     *
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function index(): ApiSuccessResponse|ApiErrorResponse
    {
        if (!Auth::id()) {
            abort(Response::HTTP_UNAUTHORIZED, trans('auth.failed'));
        }

        $favorite = Film::whereHas('users', function ($q) {
            $q->where('users.id', '=', Auth::id());
        })
            ->orderByDesc('released')
            ->paginate(8);

        $collection = FilmListResource::collection($favorite);

        return new ApiSuccessResponse($favorite);
    }

    /**
     * Добавление фильма в избранное.
     *
     * @param  int $id - $id фильма, добавляемого в избранные
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function store(int $id): ApiSuccessResponse|ApiErrorResponse
    {
        $film = Film::findOrFail($id);

        if (Favorite::firstWhere(['user_id' => Auth::id(), 'film_id' => $film->id])) {
            return new ApiErrorResponse([], Response::HTTP_UNPROCESSABLE_ENTITY, 'Этот фильм уже присутствует в Вашем списке');
        }

        $favorite = Favorite::create([
            'user_id' => Auth::id(),
            'film_id' => $film->id
        ]);

        return new ApiSuccessResponse($favorite, Response::HTTP_CREATED);
    }

    /**
     * Удаление фильма из избранного.
     *
     * @param  int  $id - $id фильма, удаляемого из избранных
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function destroy(int $id): ApiSuccessResponse|ApiErrorResponse
    {
        $film = Film::findOrFail($id);

        $favorite = Favorite::firstWhere(['user_id' => Auth::id(), 'film_id' => $film->id]);

        if (!$favorite) {
            return new ApiErrorResponse([], Response::HTTP_UNPROCESSABLE_ENTITY, 'Этот фильм отсутствует в Вашем списке');
        }

        Favorite::destroy($favorite->id);

        return new ApiSuccessResponse([], Response::HTTP_NO_CONTENT);
    }
}
