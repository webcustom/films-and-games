<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;


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

    // автогенерация slug
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    // если мы используем отношение HasMany то film меняем на films и соответственно в шаблоне тоже обращаемся $collection->films
    public function collections(): HasMany {
        return $this->hasMany(Collection::class);
    }


    protected $casts = [
        // 'published' => 'boolean',
        'published_at' => 'datetime',
        'sort_collections' => 'array',
    ];

    /**
     * Получить все категории с кешированием
     * 
     * @param int $ttl Время жизни кеша в секундах (по умолчанию 3600 = 1 час)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getCachedAll(int $ttl = 3600)
    {
        return Cache::remember('categories:all', $ttl, function () {
            return static::orderBy('title')->get();
        });
    }

    /**
     * Получить категорию по slug с кешированием
     * 
     * @param string $slug
     * @param int $ttl Время жизни кеша в секундах (по умолчанию 3600 = 1 час)
     * @return Category|null
     */
    public static function getCachedBySlug(string $slug, int $ttl = 3600)
    {
        return Cache::remember("category:slug:{$slug}", $ttl, function () use ($slug) {
            return static::where('slug', $slug)->first();
        });
    }

    /**
     * Получить категорию по ID с кешированием
     * 
     * @param int $id
     * @param int $ttl Время жизни кеша в секундах (по умолчанию 3600 = 1 час)
     * @return Category|null
     */
    public static function getCachedById(int $id, int $ttl = 3600)
    {
        return Cache::remember("category:id:{$id}", $ttl, function () use ($id) {
            return static::find($id);
        });
    }

    /**
     * Очистить весь кеш категорий
     */
    public static function clearCache(): void
    {
        Cache::forget('categories:all');
        // Очищаем кеш отдельных категорий (может быть много, поэтому используем tags если доступны)
        // В Laravel можно использовать Cache::tags(['categories'])->flush() если драйвер поддерживает tags
        // Для простоты очищаем только основные ключи
    }

    /**
     * Очистить кеш конкретной категории
     * 
     * @param Category|null $category
     */
    public static function clearCategoryCache(?Category $category): void
    {
        if (!$category) {
            return;
        }

        Cache::forget("category:slug:{$category->slug}");
        Cache::forget("category:id:{$category->id}");
        Cache::forget('categories:all');
    }

    /**
     * Boot метод для автоматической очистки кеша при изменении модели
     */
    protected static function booted()
    {
        // Очищаем кеш при создании, обновлении или удалении категории
        static::saved(function ($category) {
            static::clearCategoryCache($category);
        });

        static::deleted(function ($category) {
            static::clearCategoryCache($category);
        });
    }
}
