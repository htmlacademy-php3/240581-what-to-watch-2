<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use App\Models\User;
use App\Services\UserService;

class UserController extends Controller
{
    /**
     * Получение профиля пользователя.
     *
     * @param  int  $id - id пользователя
     *
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function show(int $id): ApiSuccessResponse|ApiErrorResponse
    {
        $user = User::find($id);

        $this->authorize('show', $user);

        $userService = new UserService($user);

        return new ApiSuccessResponse($userService->getProfileUser());
    }

    /**
     * Обновление профиля пользователя.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function update(Request $request, int $id): ApiSuccessResponse|ApiErrorResponse
    {
        return new ApiSuccessResponse();
    }
}
