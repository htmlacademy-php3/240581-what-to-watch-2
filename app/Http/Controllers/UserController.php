<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;

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
     * @param  UpdateUserRequest $request
     * @param  int  $id
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function update(UpdateUserRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        $user = Auth::user();

        $this->authorize('update', $user);

        $userService = new UserService($user);

        $user = $userService->updateUser($request, $user);

        return new ApiSuccessResponse([], Response::HTTP_ACCEPTED);
    }
}
