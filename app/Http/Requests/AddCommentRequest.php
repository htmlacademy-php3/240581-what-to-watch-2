<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddCommentRequest extends FormRequest
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
            'text' => 'required|string|min:50|max:400',
            'rating' => 'required|int|min:1|max:10',
            'comment_id' => 'nullable|int|exists:comments,id',
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
            'text.min' => 'В поле Текст должно быть не менее 50 символов.',
            'text.max' => 'В поле Текст должно быть не более 400 символов.',
            'rating.required'  => 'Поле Рейтинг обязательно для заполнения',
            'rating.min'  => 'Значение Рейтинг должно быть не менее 1',
            'rating.max'  => 'Значение Рейтинг должно быть не более 10',
        ];
    }
}
