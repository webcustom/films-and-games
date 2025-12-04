<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Game extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'title',
        'slug',
        'img_medium',
        'img_thumbnail',
        'additional_imgs',
        'additional_imgs_text',
        'additional_imgs_sort',
        'iframe_video',
        'description',
        'release',
        'genre',
        'budget',
        'maker',
        'published_at',
        'platforms',
    ];

    protected $casts = [
        // 'category_id' => 'array', // array вместо json, он теперь не поддерживается
        'cast' => 'array',
        // 'img' => 'binary',
        // 'published' => 'boolean',
        'published_at' => 'datetime',
    ];


    // автогенерация slug
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }


    // для обратной связи многие ко многим т.е. при выводе фильмов мы сможем получить коллекции привязанные к ним
    public function collections(): BelongsToMany  {
        return $this->belongsToMany(Collection::class);
    }


}

