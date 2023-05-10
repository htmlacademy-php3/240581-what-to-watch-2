<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore(Auth::user()->id),
            ],
            'password' => 'nullable|sometimes|string|min:8|confirmed',
            'file' => 'nullable|sometimes|image|max:10240',
        ];
    }
    /**
     * Сообщения об ошибках валидации
     *
     */
    public function messages()
    {
        return [
            'name.required' => 'Поле Имя обязательно для заполнения.',
            'name.max' => 'В поле Имя должно быть не более 255 символов.',
            'email.required' => 'Поле E-Mail адрес обязательно для заполнения.',
            'email.email' => 'Введён не корректный E-Mail адрес.',
            'password.min'  => 'Слишком короткий Пароль',
            'file.image'  => 'Файл должен быть изображением',
            'file.max' => 'Превышен разрешённый размер файла.',
        ];
    }
}
