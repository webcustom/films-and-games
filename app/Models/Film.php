<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * так можно добавить к автоподсказкам ide, парамептры
 * @property bool $published
 * @property Carbon $published_at
 * 
 */
class Film extends Model
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
        'rating_imdb',
        'rating_kinopoisk',
        'release',
        'duration',
        'genre',
        'country',
        'budget',
        'fees_usa',
        'fees_world',
        'director',
        'cast',
        'published_at',
    ];

    protected $casts = [
        'cast' => 'array',
        'published_at' => 'datetime',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class);
    }

    // public function isPublished(): bool
    // {
    //     return $this->published;
    // }
}
