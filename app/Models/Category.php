<?php

namespace App\Models;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

use Illuminate\Database\Eloquent\Relations\HasMany;


class Category extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'slug',
        'title',
        // 'published',
        'published_at',
        'sort_collections'
    ];

    // если мы используем отношение HasMany то film меняем на films и соответственно в шаблоне тоже обращаемся $collection->films
    public function collections(): HasMany {
        return $this->hasMany(Collection::class);
    }

    // автогенерация slug
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    protected $casts = [
        // 'published' => 'boolean',
        'published_at' => 'datetime',
        'sort_collections' => 'array',
    ];

    public static function validationRules($id = null){
        return [
            'title' => ['required', 'string', 'max:150'], 
            'slug' => ['nullable', 'string', Rule::unique('categories', 'slug')->ignore($id)],
            'published_at' => ['nullable', 'string', 'date'],
            // 'published' => ['nullable', 'boolean'],
            'sort_collections' => ['nullable', 'string'],
        ];
    }

    public static function validationCreate($request, $category = null){
        // dd($category);

        $validated = $request->validate(Category::validationRules($category ? $category->id : null));
        // dd($validated['sort_collections']);

        if(isset($validated['sort_collections'])){
            $sort_collections = json_decode($validated['sort_collections']);
        }

        $validArray = [
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'published_at' => new Carbon($validated['published_at'] ?? null), // null значение по умолчанию, Carbon приводит дату к единому типу
            // 'published' => $validated['published'] ?? false, // false значение по умолчанию
            'sort_collections' => $sort_collections ?? null,
        ];

        if(!$category){
            // dd(Collection::create($validArray));
            $categoryNew = Category::create($validArray);
        // если фильм редактируется
        }else{
            $categoryNew = $category->update($validArray);
            // удаляем привязки к категориям если фильм уже существует
            // DB::table('collection_film')->where('film_id', $film->id)->delete();
        }

        return $categoryNew;
    }
}
