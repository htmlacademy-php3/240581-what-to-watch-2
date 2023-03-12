<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\AbstractApiResponse;

class SimilarController extends Controller
{
    /**
     * Получение списка похожих фильмов.
     *
     * @return AbstractApiResponse
     */
    public function index(): AbstractApiResponse
    {
        return new ApiSuccessResponse();
    }
}
