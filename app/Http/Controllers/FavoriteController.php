<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use App\Models\Favorite;
use App\Models\Film;
use App\Models\User;
use App\Http\Resources\FilmListResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        // Вот хорошо бы так, да ТЗ не велит!
        /*
        $favorite = Favorite::firstOrCreate(
            ['user_id' => Auth::id(), 'film_id' => $film->id],
            ['user_id' => Auth::id(), 'film_id' => $film->id]
        );*/

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
     * @param  int  $id
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function destroy(int $id/* TO DO , User $User */): ApiSuccessResponse|ApiErrorResponse
    {
        return new ApiSuccessResponse();
    }
}
