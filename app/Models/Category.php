<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

}
