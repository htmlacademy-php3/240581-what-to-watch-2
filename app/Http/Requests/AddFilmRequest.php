<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddFilmRequest extends FormRequest
{
    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'imdbId' => 'required|regex:/^tt\d+$/|unique:films,imdb_id'
        ];
    }

    /**
     * Сообщения об ошибках валидации
     *
     */
    public function messages()
    {
        return [
            'imdbId.required' => 'Поле imdbId обязательно для заполнения.',
            'imdbId.regex' => 'Поле imdbId должно быть в формате tt0000000.',
            'imdbId.unique' => 'Такой фильм уже есть в базе',
        ];
    }
}
