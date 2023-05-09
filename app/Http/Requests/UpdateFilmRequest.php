<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Film;

class UpdateFilmRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'poster_image' => 'nullable|string|max:255',
            'preview_image' => 'nullable|string|max:255',
            'background_image' => 'nullable|string|max:255',
            'video_link' => 'nullable|string|max:255',
            'preview_video_link' => 'nullable|string|max:255',
            'director' => 'nullable|string|max:255',
            'background_color' => 'nullable|string|max:9',
            'description' => 'nullable|string|max:1000',
            'run_time' => 'nullable|int',
            'released' => 'nullable|int',
            'imdb_id' => [
                'required',
                'string',
                'regex:/^tt\d+$/',
                Rule::unique('films', 'imdb_id')->ignore($this->id),
            ],
            'status' => [
                'required',
                'string',
                Rule::in(Film::FILM_STATUS_MAP),
            ],
            'starring' => 'nullable|array',
            'genre' => 'nullable|array',
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
            'poster_image.max' => 'Превышена длина имени файла.',
            'preview_image.max' => 'Превышена длина имени файла.',
            'background_image.max' => 'Превышена длина имени файла.',
            'video_link.max' => 'Превышена длина имени файла.',
            'director.max' => 'Длина имени должна быть не более 255 символов.',
            'background_color.max' => 'Должно быть не более 9 символов.',
            'description.max' => 'Описание должно быть не более 1000 символов.',
            'imdbId.required' => 'Поле imdbId обязательно для заполнения.',
            'imdbId.regex' => 'Поле imdbId должно быть в формате tt0000000.',
            'imdbId.unique' => 'Такой фильм уже есть в базе',
            'status.required' => 'Поле Статус обязательно для заполнения.',
        ];
    }
}
