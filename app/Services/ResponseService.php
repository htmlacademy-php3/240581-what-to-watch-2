<?php

namespace App\services;

use Symfony\Component\HttpFoundation\Response;
use App\Http\Responses\AbstractApiResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Responses\ApiErrorResponse;

/**
 * Прикладной сервис формирования сообщений об ошибке
 */
class ResponseService
{
    private const STATUS_MESSAGES = [
        Response::HTTP_OK => 'Завершено успешно.',
        Response::HTTP_UNAUTHORIZED => 'Ошибка авторизации.',
        Response::HTTP_FORBIDDEN => 'У Вас недостаточно прав.',
        Response::HTTP_NOT_FOUND => 'Запрашиваемая страница не существует.',
        //Response::HTTP_UNPROCESSABLE_ENTITY => 'Фильм уже находится в избранном.',
    ];

    public function __construct(
        private array $data = [],
        private int $code = Response::HTTP_OK,
        private string $message = ''
    ) {
    }

    /**
     * Метод получения сообщений об ошибке, соответстующих коду
     *
     * @return string
     */
    private function getMessage(): string
    {
        if (array_key_exists($this->code, self::STATUS_MESSAGES)) {
            return self::STATUS_MESSAGES[$this->code];
        }
        return Response::$statusTexts[$this->code];
    }

    /**
     * Метод проверки на отсутствие фильма в базе с установкой кода ошибки 404
     *
     * @return int
     */
    private function checkNotFound(): int
    {
        if (array_key_exists('Error', $this->data)) {
            $this->code = Response::HTTP_NOT_FOUND;
        }
        return $this->code;
    }

    /**
     * Метод создания нужного наследника объекта класса AbstractApiResponse в зависимости от кода состояния HTTP
     *
     * @return AbstractApiResponse
     */
    public function createResponse(): AbstractApiResponse
    {
        $this->code = $this->checkNotFound($this->data, $this->code);

        if (!$this->message) {
            $this->message = $this->getMessage($this->code);
        }

        if ($this->code >= Response::HTTP_BAD_REQUEST) {
            return new ApiErrorResponse($this->data, $this->code, $this->message);
        }
        return new ApiSuccessResponse($this->data, $this->code, ['message' => $this->getMessage($this->code)]);
    }
}
