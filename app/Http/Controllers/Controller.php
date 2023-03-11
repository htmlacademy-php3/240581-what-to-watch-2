<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\AbstractApiResponse;
use App\Services\ResponseService;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function getResponse($data = [], int $code = Response::HTTP_OK, string $message = ''): AbstractApiResponse
    {
        $responseService = new ResponseService($data, $code, $message);

        return $responseService->createResponse();
    }
}
