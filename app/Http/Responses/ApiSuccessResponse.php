<?php

namespace App\Http\Responses;

use App\Http\Responses\AbstractApiResponse;

class ApiSuccessResponse extends AbstractApiResponse
{
    /**
     * Формирование содержимого ответа.
     *
     * @return array
     */
    protected function makeResponseData(): array
    {
        return $this->prepareData();
        /*
        return [
            'data' => $this->prepareData()
        ];
        */
    }
}
