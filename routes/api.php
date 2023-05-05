<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\SimilarController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('comments/{id}', [CommentController::class, 'index']);
Route::get('genres', [GenreController::class, 'index']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::prefix('films')->middleware('auth:sanctum')->group(function () {
    Route::withoutMiddleware('auth:sanctum')->group(function () {
        Route::get('/', [FilmController::class, 'index']);
        Route::get('/{id}', [FilmController::class, 'show']);
        Route::get('/{id}/similar', [SimilarController::class, 'index']);
    });
    Route::post('/', [FilmController::class, 'store']);
    Route::patch('/{id}', [FilmController::class, 'update']);
    Route::post('/{id}/favorite', [FavoriteController::class, 'store']);
    Route::delete('/{id}/favorite', [FavoriteController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::patch('genres/{id}', [GenreController::class, 'update']);

    Route::resource('favorite', FavoriteController::class);

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('comments/{id}', [CommentController::class, 'store']);
    Route::patch('comments/{id}', [CommentController::class, 'update']);
    Route::delete('comments/{id}', [CommentController::class, 'destroy']);

    Route::resource('user', UserController::class);

    Route::prefix('promo')->group(function () {
        Route::get('/', [PromoController::class, 'index'])->withoutMiddleware('auth:sanctum');
        Route::post('/{id}', [PromoController::class, 'store']);
        Route::delete('/{id}', [PromoController::class, 'destroy']);
    });
});
