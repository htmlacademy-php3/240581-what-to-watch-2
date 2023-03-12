<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractApiResponse implements Responsable
{
    public function __construct(
        protected mixed $data = [],
        public int $code = Response::HTTP_OK,
    ) {
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  Request $request
     * @return Response
     */
    public function toResponse($request): Response
    {
        return response()->json($this->makeResponseData(), $this->code);
    }

    /**
     * Преобразование возвращаемых данных к массиву.
     *
     * @return array
     */
    protected function prepareData(): array
    {
        if ($this->data instanceof Arrayable) {
            return $this->data->toArray();
        }
        return $this->data;
    }

    /**
     * Формирование содержимого ответа.
     *
     * @return array
     */
    abstract protected function makeResponseData(): array;
}
