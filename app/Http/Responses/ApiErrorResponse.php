<?php

namespace App\Http\Responses;

use Symfony\Component\HttpFoundation\Response;
use Throwable;
use App\Http\Responses\AbstractApiResponse;

class ApiErrorResponse extends AbstractApiResponse
{
    /**
     * ExceptionResponse constructor.
     *
     * @param  $data
     * @param  string|null $message
     * @param  int $code
     */
    public function __construct(
        protected mixed $data,
        public int $code = Response::HTTP_BAD_REQUEST,
        private ?string $message = null
    ) {
        parent::__construct([], $code);
    }

    /**
     * Формирование содежимого ответа.
     *
     * @return array
     */
    protected function makeResponseData(): array
    {
        $response = [
            'message' => $this->message,
        ];
        return $response;
    }
}
