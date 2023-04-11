<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;

class GenreController extends Controller
{
    /**
     * Получение списка жанров.
     *
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function index(): ApiSuccessResponse|ApiErrorResponse
    {
        return new ApiSuccessResponse();
    }

    /**
     * Редактирование жанра.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function update(Request $request,int $id): ApiSuccessResponse|ApiErrorResponse
    {
        $this->authorize('update', Genre::class);
        return new ApiSuccessResponse();
    }
}
