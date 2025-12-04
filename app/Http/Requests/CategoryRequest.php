<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // или проверка прав
    }

    public function rules(): array
    {
        $id = $this->route('category')?->id;

        return [
            'title' => ['required', 'string', 'max:150'], 
            'slug' => ['nullable', 'string', Rule::unique('categories', 'slug')->ignore($id)],
            'published_at' => ['nullable', 'string', 'date'],
            // 'published' => ['nullable', 'boolean'],
            'sort_collections' => ['nullable', 'string'],
        ];
    }
}