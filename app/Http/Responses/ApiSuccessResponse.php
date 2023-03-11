<?php

namespace App\Http\Responses;

use Illuminate\Http\Response;
use App\Http\Responses\AbstractApiResponse;

class ApiSuccessResponse extends AbstractApiResponse
{
    /**
     * @param  mixed  $data
     * @param  array  $metadata
     * @param  int  $code
     * @param  array  $headers
     */
    public function __construct(
        protected mixed $data,
        public int $code = Response::HTTP_OK,
        private array $metadata
    ) {
    }

    /**
     * Формирование содержимого ответа.
     *
     * @return array
     */
    protected function makeResponseData(): array
    {
        return [
            'data' => $this->prepareData(),
            'metadata' => $this->metadata,
        ];
    }
}
