<?php

namespace App\Models;

use App\Services\ImageProcessor;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Routing\Route;

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

    public function category(): BelongsTo {
        return $this->belongsTo(Category::class);
    }

    // автогенерация slug
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
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


    public static function validationRules($id = null)
    {
        // dd($id);
        // $collection = Collection::find($id);
        return [
            // пишем арпвила валидации для каждого элемента в запросе $request
            // если эти правила валидации часто повторяются их можно вынести в модель , как это сделать см. урок 16 конец ролика
            'title' => ['required', 'string', 'max:250'], // обязательный, строка, максимум 100 символов
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
        ];
    }

    public static function validationCreate($request, $collection = null)
    {
        // $id = Collection::find('id');
        // $id = Collection::getKey();
        // $id = 20;//$request->input('id'); // получаем id из запроса, если он есть, в противном случае устанавливаем значение по умолчанию null
        // dd($request->category);
        
        $validated = $request->validate(Collection::validationRules($collection ? $collection->id : null));

        // $resources = explode(",", $validated['resource_id']);

        // if($request->hasFile('img')){
        //     $image = $request->file('img');
        //     $path = $image->store('collections'); //загружаем файл в директорию storage/app/public/collections после выполняем команду php artisan storage:link и файлы будут доступны у нас из дериктории storage/films/...
        // }else if(isset($collection->img) && !isset($validated['delete_img'])){ //если файл не загружался и поле с путем для удаления файла пустое
        //     $path = $collection->img;
        // }

        // есил файл загрузили в форме
        if($request->hasFile('img')){
            // инкапсулировал логику сжатия изображений в класс App/Services/ImageProcessor
            $imageProcessor = new ImageProcessor();
            //передаем само изображение, название папки куда будут сохранятся изображения, и массив с размерами изображений
            $paths = $imageProcessor->processImage($request->file('img'), 'collections', [[800, 600], [300, 200]]); 
        
            // $path_original = $paths[0];
            $path_medium = $paths[0];
            $path_thumbnail = $paths[1];
        //если файл не загружался и поле с путем для удаления файла пустое
        }else if(!isset($collection->img) && !isset($validated['delete_img']) && isset($collection->img_medium) && isset($collection->img_thumbnail)){
            // $path_original = $collection->img;
            $path_medium = $collection->img_medium;
            $path_thumbnail = $collection->img_thumbnail;
        }
       
        // если input с путем для уделения файла не пустой удаляем файл из хранилица
        // if(isset($validated['delete_img'])){
        //     Storage::delete($validated['delete_img']);
        // }

        // $sort_films = explode(",", $validated['sort_films']);
        // $jsonData = json_decode($request->getContent(), true);
        // dd($validated['sort_films']);
        // dd(json_decode($validated['sort_films']));

        if(isset($validated['sort_elems'])){
            $sort_elems = json_decode($validated['sort_elems']);
        }

        $validArray = [
            'title' => $validated['title'],
            'title_seo' => $validated['title_seo'],

            'slug' => $validated['slug'],
            // 'img' => $path_original ?? null, //$validated['img'],
            'img_medium' => $path_medium ?? null, //$validated['img'],
            'img_thumbnail' => $path_thumbnail ?? null,
            'description' => $validated['description'] ?? null,
            // 'resource_id' => $resources,
            'published_at' => new Carbon($validated['published_at'] ?? null), // null значение по умолчанию, Carbon приводит дату к единому типу
            'published' => $validated['published'] ?? false, // false значение по умолчанию
            'sort_elems' => $sort_elems ?? null,
            // 'sort_games' => $sort_games ?? null,
            'category_id' => $validated['category_id'] ?? null,
        ];

        // если input с путем для удаления файла не пустой удаляем файл из хранилица
        if(isset($validated['delete_img'])){
            $imgs_paths = [$collection->img_medium, $collection->img_thumbnail];
            Collection::deleteFiles($imgs_paths);
        }

        // dump(!$collection);
        if(!$collection){
            // dd(Collection::create($validArray));
            $collectionNew = Collection::create($validArray);
        // если фильм редактируется
        }else{
            $collectionNew = $collection->update($validArray);
            // удаляем привязки к категориям если фильм уже существует
            // DB::table('collection_film')->where('film_id', $film->id)->delete();
        }

        return $collectionNew;


    }


    public static function deleteFiles($imgs_paths)
    {
        foreach($imgs_paths as $path){
            if(isset($path) && file_exists(public_path($path))) {
                unlink(public_path($path));
            }
        }
    }


}


