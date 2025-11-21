<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // или проверка прав
    }

    public function rules(): array
    {
        $id = $this->route('game')?->id;

        return [
            // пишем арпвила валидации для каждого элемента в запросе $request
            // если эти правила валидации часто повторяются их можно вынести в модель, как это сделать см. урок 16 конец ролика
            'title' => ['required', 'string', 'max:150'], // обязательный, строка, максимум 100 символов
            'slug' => ['nullable', 'string', Rule::unique('games', 'slug')->ignore($id)],
            'img' => ['nullable', 'image:jpg, jpeg, png, webp, svg', 'max:2048'],
            'additional_imgs' => ['nullable','array'],
            'additional_imgs.*' => ['nullable', 'image:jpg, jpeg, png, webp, svg', 'max:2048'],
            'additional_imgs_text' => ['nullable', 'array'],
            'additional_imgs_text.*' => ['nullable', 'string', 'max:200'],
            'additional_imgs_sort' => ['nullable','array'],
            'additional_imgs_sort.*' => ['nullable','string', 'max:10'],
            'delete_img' => ['nullable', 'string', 'in:1'],
            'iframe_video' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string', 'max:10000'],
            'release' => ['nullable', 'string', 'max:30'],
            'genre' => ['nullable', 'string', 'max:60'],
            'budget' => ['nullable', 'string'], 
            'maker' => ['nullable', 'string', 'max:100'],
            'published_at' => ['nullable', 'string', 'date'],
            'collections' => ['array'],
            'collections.*' => ['exists:collections,slug'], //проверяем что каждый элемент в массиве collections существует в таблице коллекций в столбце slug
            'platforms' => ['required', 'string', 'max:150'],
        ];
    }
}