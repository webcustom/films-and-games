<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CollectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // или проверка прав
    }

    public function rules(): array
    {
        $id = $this->route('collection')?->id;

        return [
            // пишем правила валидации для каждого элемента в запросе $request
            'title' => ['required', 'string', Rule::unique('collections', 'title')->ignore($id)], // обязательный, строка, максимум 100 символов
            'title_seo' => ['nullable', 'string', 'max:250'], // обязательный, строка, максимум 100 символов
            'slug' => ['nullable', 'string', Rule::unique('collections', 'slug')->ignore($id)],
            'img' => ['nullable', 'image:jpg, jpeg, png, webp, svg', 'max:2048'],
            'delete_img' => ['nullable', 'string', 'in:1'],
            'description' => ['nullable', 'string', 'max:10000'],
            // 'resource_id' => ['nullable', 'string'],
            'published_at' => ['nullable', 'string', 'date'],
            'published' => ['nullable', 'boolean'],
            'sort_elems' => ['nullable', 'string'], //['nullable', 'json'],
            // 'sort_games' => ['nullable', 'string'], //['nullable', 'json'],
            'category_id' => ['nullable', 'exists:categories,id'],
            // 'search' => ['nullable', 'string', 'max:50'], //строка поиска
            // 'selectionByCat' => ['nullable', 'string'],
        ];
    }
}