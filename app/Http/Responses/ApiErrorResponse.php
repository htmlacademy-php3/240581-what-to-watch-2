<?php

namespace App\Http\Responses;

use Symfony\Component\HttpFoundation\Response;
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
        protected mixed $data = [],
        public int $code = Response::HTTP_BAD_REQUEST,
        private ?string $message = null
    ) {
    }

    /**
     * Формирование содежимого ответа.
     *
     * @return array
     */
    protected function makeResponseData(): array
    {
        $response = [
            'message' => $this->getMessage(),
        ];
        return $response;
    }

    /**
     * Метод получения сообщений об ошибке, соответстующих коду
     *
     * @return string
     */
    private function getMessage(): string
    {
        if ($this->message) {
            return $this->message;
        }

        if (array_key_exists('Error', $this->data) || $this->code === Response::HTTP_NOT_FOUND) {
            return 'Запрашиваемая страница не существует.';
        }
        return Response::$statusTexts[$this->code];
    }
}
