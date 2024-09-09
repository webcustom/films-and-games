<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

use App\Services\ImageProcessor;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Game extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        // 'collection_id',
        'title',
        'slug',
        'img',
        'img_medium',
        'img_thumbnail',
        'description',
        // 'rating_imdb',
        // 'rating_kinopoisk',
        'release',
        // 'duration',
        'genre',
        // 'country',
        'budget',
        // 'fees_usa',
        // 'fees_world',
        'maker',
        // 'cast',
        'published',
        'published_at',
        // 'collections',
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



    protected $casts = [
        // 'category_id' => 'array', // array вместо json, он теперь не поддерживается
        'cast' => 'array',
        // 'img' => 'binary',
        'published' => 'boolean',
        'published_at' => 'datetime',
    ];


    public static function validationRules($id = null)
    {
        return [
            // пишем арпвила валидации для каждого элемента в запросе $request
            // если эти правила валидации часто повторяются их можно вынести в модель , как это сделать см. урок 16 конец ролика
            // 'collection_id' => ['nullable', 'integer'],
            'title' => ['required', 'string', 'max:150'], // обязательный, строка, максимум 100 символов
            'slug' => ['nullable', 'string', Rule::unique('games', 'slug')->ignore($id)],
            'img' => ['nullable', 'image:jpg, jpeg, png, webp, svg', 'max:2048'],
            'delete_img' => ['nullable', 'string', 'in:1'],
            'description' => ['nullable', 'string', 'max:10000'],
            // 'category_id' => ['nullable', 'integer'],
            // 'rating_imdb' => ['nullable', 'string', 'max:30'],
            // 'rating_kinopoisk' => ['nullable', 'string', 'max:30'],
            'release' => ['nullable', 'string', 'max:30'],
            // 'duration' => ['nullable', 'string', 'max:30'],
            'genre' => ['nullable', 'string', 'max:60'],
            // 'country' => ['nullable', 'string', 'max:50'], ///////////////
            'budget' => ['nullable', 'string'], 
            // 'fees_usa' => ['nullable', 'string'],
            // 'fees_world' => ['nullable', 'string'],
            'maker' => ['nullable', 'string', 'max:100'],
            // 'cast' => ['nullable', 'string'],
            'published_at' => ['nullable', 'string', 'date'],
            'published' => ['nullable', 'boolean'],
            'collections' => ['array'],
            'collections.*' => ['exists:collections,slug'], //проверяем что каждый элемент в массиве collections существует в таблице коллекций в столбце slug
        ];
    }


    public static function validationCreate($request, $game = null)
    {

        $validated = $request->validate(Game::validationRules($game ? $game->id : null));
        // $cast = explode(",", $validated['cast']);


        // dd($validated);
        // dd($request);

        // есил файл загрузили в форме
        if($request->hasFile('img')){

        // dd($request);

            // инкапсулировал логику сжатия изображений в класс App/Services/ImageProcessor
            $imageProcessor = new ImageProcessor();
            // dd($request->img);

            //передаем само изображение, название папки куда будут сохранятся изображения, и массив с размерами изображений
            $paths = $imageProcessor->processImage($request->file('img'), 'games', [[300, 200], [1000, 600]]); 

            $path_original = $paths[0];
            $path_medium = $paths[1];
            $path_thumbnail = $paths[2];


        //если файл не загружался и поле с путем для удаления файла пустое
        }else if(isset($game->img) && !isset($validated['delete_img'])){


            $path_original = $game->img;
            $path_medium = $game->img_medium;
            $path_thumbnail = $game->img_thumbnail;


        }

        $validArray = [
            'title' => $validated['title'],
            'slug' =>  $validated['slug'],
            'img' => $path_original ?? null, //$validated['img'],
            'img_medium' => $path_medium ?? null, //$validated['img'],
            'img_thumbnail' => $path_thumbnail ?? null, //$validated['img'],

            'description' => $validated['description'] ?? null,
            // 'category_id' => Category::query()->value('id'), //$validated['category_id'],
            // 'rating_imdb' => $validated['rating_imdb'] ?? null,
            // 'rating_kinopoisk' => $validated['rating_kinopoisk'] ?? null,
            'release' => $validated['release'] ?? null,
            'duration' => $validated['duration'] ?? null,
            'genre' => $validated['genre'] ?? null,
            // 'country' => $validated['country'] ?? null, ///////////////
            'budget' => $validated['budget'] ?? null, 
            // 'fees_usa' => $validated['fees_usa'] ?? null,
            // 'fees_world' => $validated['fees_world'] ?? null,
            'maker' => $validated['maker'] ?? null,
            // 'cast' => $cast ?? null, //$validated['cast'] ?? null,
            'published_at' => new Carbon($validated['published_at'] ?? null), // null значение по умолчанию, Carbon приводит дату к единому типу
            // 'published_at' => isset($validated['published_at']) ? Carbon::parse($validated['published_at']) : null,
            'published' => $validated['published'] ?? false, // false значение по умолчанию
        ];


        // если input с путем для удаления файла не пустой удаляем файл из хранилица
        if(isset($validated['delete_img'])){
            $imgs_paths = [$game->img, $game->img_medium, $game->img_thumbnail];
            Game::deleteFiles($imgs_paths);
        }
        


        // если фильм создается 
        if(!$game){
            // dd(Film::create($validArray));
            $gameNew = Game::create($validArray);
        // если фильм редактируется
        }else{
            $gameNew = $game->update($validArray);
            // удаляем привязки к категориям если фильм уже существует
            DB::table('collection_game')->where('game_id', $game->id)->delete();

        }

        
        // Привязка фильма к коллекциям, если массив выбранных коллекций не пустой
        if (isset($validated['collections'])) {
            // $collections = Collection::all();
            // находим id коллекций у которых параметр slug совпадает с тем что приходит в массиве $validated['collections']
            // $ids = $collections->whereIn('slug', $validated['collections'])->pluck('id'); // вернет id элемента

            // получаем коллекций, у которых значение поля 'slug' совпадает с одним из значений в массиве $validated['collections']
            $collections = Collection::whereIn('slug', $validated['collections'])->get(); //вернет сам элемент
            // здесь мы извлекаем значения поля 'id' из коллекций, найденных на предыдущем шаге, с помощью метода pluck('id'). Метод all() преобразует коллекцию в обычный массив
            $collectionIds = $collections->pluck('id')->all();

            // dump($film->collections());
            // dd($collectionIds);
            if ($game) {
                // Здесь мы используем отношение collections() модели фильма для синхронизации коллекций. Метод sync() синхронизирует коллекцию с заданными идентификаторами. В результате, коллекции фильма будут соответствовать значениям в $collectionIds
                $game->collections()->sync($collectionIds);
            } else {
                // Здесь мы используем метод attach() для добавления коллекций к новому фильму $filmNew
                $gameNew->collections()->attach($collectionIds);
            }

        }

        return $gameNew;

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

