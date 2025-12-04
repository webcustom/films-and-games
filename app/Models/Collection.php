<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;


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


}


