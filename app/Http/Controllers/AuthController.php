<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use \App\Models\User;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    /**
     * Аутентификация пользователя
     *
     * @param  LoginRequest  $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function login(LoginRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        if (!Auth::attempt($request->validated())) {
            abort(Response::HTTP_UNAUTHORIZED, trans('auth.failed'));
        }

        $token = Auth::user()->createToken('auth-token');

        $data = ['token' => $token->plainTextToken];
        return new ApiSuccessResponse($data);
    }

    /**
     * Выход пользователяиз системы
     *
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function logout(): ApiSuccessResponse|ApiErrorResponse
    {
        Auth::user()->tokens()->delete();

        return new ApiSuccessResponse([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Регистрация пользователя
     *
     * @param  RegisterRequest  $request
     * @return ApiSuccessResponse|ApiErrorResponse
     */
    public function register(RegisterRequest $request): ApiSuccessResponse|ApiErrorResponse
    {
        $params = $request->safe()->except('file');
        $params['password'] = bcrypt($params['password']);
        $user = User::create($params);
        $token = $user->createToken('auth-token');

        $data = [
            'user' => $user,
            'token' => $token->plainTextToken,
        ];

        return new ApiSuccessResponse($data, Response::HTTP_CREATED);
    }
}
