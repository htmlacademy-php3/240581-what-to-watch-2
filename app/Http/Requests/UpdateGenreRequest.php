<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGenreRequest extends FormRequest
{
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
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('genres', 'title')->ignore($this->id),
            ],
        ];
    }

    /**
     * Сообщения об ошибках валидации
     *
     */
    public function messages()
    {
        return [
            'text.required' => 'Поле Текст обязательно для заполнения.',
            'text.max' => 'В поле Текст должно быть не более 255 символов.',
        ];
    }
}
