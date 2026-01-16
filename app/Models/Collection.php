<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Support\Facades\Cache;


class Collection extends Model
{
    use HasFactory, HasSlug;
    
    protected $fillable = [
        'slug',
        'title',
        'title_seo',
        // 'img',
        'img_medium',
        'img_thumbnail',
        'description',
        // 'resource_id',
        'published',
        'published_at',
        'sort_elems',
        // 'sort_games',
        'category_id',
        // 'delete_elems',
        // 'collection_id',
    ];

    // автогенерация slug
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function category(): BelongsTo {
        return $this->belongsTo(Category::class);
    }


    // public function film(): BelongsTo {
    //     return $this->belongsTo(Film::class);
    // }
    // для связи один к одному т.е. при выводе коллекции мы сможем получить фильмы привязанные к ней
    // public function film(): HasOne {
    //     return $this->hasOne(Film::class);//->withDefault(null);
    // }
    // если мы используем отношение HasMany то film меняем на films и соответственно в шаблоне тоже обращаемся $collection->films
    // public function films(): HasMany {
    //     return $this->hasMany(Film::class);
    // }

    // для обратной связи многие ко многим т.е. при выводе коллекций мы сможем получить фильмы привязанные к ним
    // возможно тут стоило использовать полиморфную связь morphMany что бы не создавать 2 таблицы в базе данных
    public function films(): BelongsToMany  {
        return $this->BelongsToMany (Film::class);
    }
    public function games(): BelongsToMany  {
        return $this->BelongsToMany (Game::class);
    }
    
    // наши данные приводятся к нужным типам
    protected $casts = [
        // 'category_id' => 'array', // array вместо json, он теперь не поддерживается
        'sort_elems' => 'array',
        // 'sort_games' => 'array',
        'published' => 'boolean',
        'published_at' => 'datetime',
        // 'collection_id' => 
    ];

    /**
     * Получить опубликованные коллекции с кешированием
     * 
     * @param int $limit
     * @param int $ttl Время жизни кеша в секундах (по умолчанию 1800 = 30 минут)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getCachedPublished(int $limit = 19, int $ttl = 1800)
    {
        return Cache::remember("collections:published:limit:{$limit}", $ttl, function () use ($limit) {
            return static::query()
                ->where('published', true)
                ->whereNotNull('category_id')
                ->with('category')
                ->latest('published_at')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Получить коллекции категории с кешированием
     * 
     * @param int $categoryId
     * @param int $ttl Время жизни кеша в секундах (по умолчанию 1800 = 30 минут)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getCachedByCategory(int $categoryId, int $ttl = 1800)
    {
        return Cache::remember("collections:category:{$categoryId}", $ttl, function () use ($categoryId) {
            return static::where('category_id', $categoryId)
                ->where('published', true)
                ->latest('published_at')
                ->get();
        });
    }

    /**
     * Очистить кеш коллекций
     */
    public static function clearCache(): void
    {
        // Очищаем основные ключи кеша коллекций
        // В реальном приложении можно использовать Cache::tags(['collections'])->flush()
        // если драйвер кеша поддерживает теги (Redis, Memcached)
        Cache::forget('collections:published:limit:19');
    }

    /**
     * Очистить кеш конкретной коллекции
     * 
     * @param Collection|null $collection
     */
    public static function clearCollectionCache(?Collection $collection): void
    {
        if (!$collection) {
            return;
        }

        // Очищаем кеш коллекций категории
        if ($collection->category_id) {
            Cache::forget("collections:category:{$collection->category_id}");
        }

        // Очищаем общий кеш опубликованных коллекций
        static::clearCache();
    }

    /**
     * Boot метод для автоматической очистки кеша при изменении модели
     */
    protected static function booted()
    {
        // Очищаем кеш при создании, обновлении или удалении коллекции
        static::saved(function ($collection) {
            static::clearCollectionCache($collection);
        });

        static::deleted(function ($collection) {
            static::clearCollectionCache($collection);
        });
    }


}


