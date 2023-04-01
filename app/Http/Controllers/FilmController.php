<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use App\services\PermissionCheckService;

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
     * @param  \Illuminate\Http\Request  $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function store(Request $request): ApiSuccessResponse|ApiErrorResponse
    {
        if (!PermissionCheckService::checkPermission()) {
            abort(Response::HTTP_FORBIDDEN, trans('auth.failed'));
        }

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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function update(Request $request,int $id): ApiSuccessResponse|ApiErrorResponse
    {
        if (!PermissionCheckService::checkPermission()) {
            abort(Response::HTTP_FORBIDDEN, trans('auth.failed'));
        }

        return new ApiSuccessResponse();
    }
}
