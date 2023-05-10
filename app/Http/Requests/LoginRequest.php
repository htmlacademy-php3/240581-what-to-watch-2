<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string',
        ];
    }

    /**
     * Сообщения об ошибках валидации
     *
     */
    public function messages()
    {
        return [
            'email.required' => 'Поле E-Mail адрес обязательно для заполнения.',
            'email.email' => 'Введён не корректный E-Mail адрес.',
            'password.required'  => 'Поле Пароль обязательно для заполнения',
        ];
    }
}
