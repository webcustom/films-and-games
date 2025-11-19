<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // или проверка прав
    }

    public function rules(): array
    {
        $id = $this->route('film')?->id;

        return [
            'title' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', Rule::unique('films', 'slug')->ignore($id)],
            'img' => ['nullable', 'image:jpg,jpeg,png,webp,svg', 'max:2048'],
            'additional_imgs' => ['nullable','array'],
            'additional_imgs.*' => ['nullable', 'image:jpg,jpeg,png,webp,svg', 'max:2048'],
            'additional_imgs_text' => ['nullable', 'array'],
            'additional_imgs_text.*' => ['nullable', 'string', 'max:200'],
            'additional_imgs_sort' => ['nullable','array'],
            'additional_imgs_sort.*' => ['nullable','string', 'max:10'],
            'delete_img' => ['nullable', 'string', 'in:1'],
            'delete_additional_img' => ['nullable', 'string'],
            'iframe_video' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string', 'max:10000'],
            'rating_imdb' => ['nullable', 'string', 'max:30'],
            'rating_kinopoisk' => ['nullable', 'string', 'max:30'],
            'release' => ['nullable', 'string', 'max:30'],
            'duration' => ['nullable', 'string', 'max:30'],
            'genre' => ['nullable', 'string', 'max:200'],
            'country' => ['nullable', 'string', 'max:200'],
            'budget' => ['nullable', 'string'],
            'fees_usa' => ['nullable', 'string'],
            'fees_world' => ['nullable', 'string'],
            'director' => ['nullable', 'string', 'max:100'],
            'cast' => ['nullable', 'string'],
            'published_at' => ['nullable', 'date'],
            'collections' => ['array'],
            'collections.*' => ['exists:collections,slug'],
        ];
    }
}