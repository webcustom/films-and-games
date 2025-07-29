<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

use App\Services\ImageProcessor;
// use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

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
        // 'collection_id',
        'title',
        'slug',
        // 'img',
        'img_medium',
        'img_thumbnail',
        'additional_imgs',
        'additional_imgs_text',
        'additional_imgs_sort',
        // 'edit_addition_imgs', //замененные доп. изображения
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
        // 'published',
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
    // для обратной связи один к одному или один ко многим т.е. при выводе фильмов мы сможем получить коллекции привязанные к ним
    // public function collection(): BelongsTo {
    //     return $this->belongsTo(Collection::class);
    // }
    // для обратной связи многие ко многим т.е. при выводе фильмов мы сможем получить коллекции привязанные к ним
    public function collections(): BelongsToMany  {
        return $this->belongsToMany(Collection::class);
    }


    //$casts - это ассоциативный массив, который определяет, какие атрибуты модели должны быть приведены к определенным типам данных при извлечении и сохранении в базе данных. 
    // Это удобно, если вы хотите, чтобы определенные поля автоматически преобразовывались в определенные типы данных при их доступе.
    protected $casts = [
        // 'category_id' => 'array', // array вместо json, он теперь не поддерживается
        'cast' => 'array',
        // 'additional_imgs_sort' => 'array',
        // 'img' => 'binary',
        // 'published' => 'boolean',
        'published_at' => 'datetime',
    ];





    // добавляем кастомный метод который проверяет что пост опубликован и у него есть дата публикации
    // благодаря этому в контроллере можно обращаться к этому методу Post::query()->first()->isPublished()
    public function isPublished(): bool {
        return $this->published; //&& $this->published_at;
    }
    // protected $dates = [
    //     'published_at',
    // ];

    public static function validationRules($id = null)
    {
        return [
            // пишем арпвила валидации для каждого элемента в запросе $request
            // если эти правила валидации часто повторяются их можно вынести в модель , как это сделать см. урок 16 конец ролика
            // 'collection_id' => ['nullable', 'integer'],
            'title' => ['required', 'string', 'max:150'], // обязательный, строка, максимум 100 символов
            'slug' => ['nullable', 'string', Rule::unique('films', 'slug')->ignore($id)],
            'img' => ['nullable', 'image:jpg, jpeg, png, webp, svg', 'max:2048'],
            // 'main_image_exists' => ['nullable', 'string'],
            'additional_imgs' => ['nullable','array'],
            'additional_imgs.*' => ['nullable', 'image:jpg, jpeg, png, webp, svg', 'max:2048'],
            'additional_imgs_text' => ['nullable', 'array'],
            'additional_imgs_text.*' => ['nullable', 'string', 'max:200'],
            'additional_imgs_sort' => ['nullable','array'],
            'additional_imgs_sort.*' => ['nullable','string', 'max:10'],
            // 'edit_addition_imgs' => ['nullable', 'string'],
            // 'edit_addition_imgs.*' => ['nullable', 'integer'],
            'delete_img' => ['nullable', 'string', 'in:1'], //in:1 может быть только 1
            'delete_additional_img' => ['nullable', 'string'],
            'iframe_video' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string', 'max:10000'],
            // 'category_id' => ['nullable', 'integer'],
            'rating_imdb' => ['nullable', 'string', 'max:30'],
            'rating_kinopoisk' => ['nullable', 'string', 'max:30'],
            'release' => ['nullable', 'string', 'max:30'],
            'duration' => ['nullable', 'string', 'max:30'],
            'genre' => ['nullable', 'string', 'max:60'],
            'country' => ['nullable', 'string', 'max:50'], ///////////////
            'budget' => ['nullable', 'string'], 
            'fees_usa' => ['nullable', 'string'],
            'fees_world' => ['nullable', 'string'],
            'director' => ['nullable', 'string', 'max:100'],
            'cast' => ['nullable', 'string'],
            'published_at' => ['nullable', 'string', 'date'],
            // 'published' => ['nullable', 'boolean'],
            'collections' => ['array'],
            'collections.*' => ['exists:collections,slug'], //проверяем что каждый элемент в массиве collections существует в таблице коллекций в столбце slug
        ];
    }

    public static function validationCreate($request, $film = null)
    {

        // dd($request);

        $validated = $request->validate(Film::validationRules($film ? $film->id : null));
        $cast = explode(",", $validated['cast']);

        
        // dd($validated);
        // dd($validated['add_imgs_text']);

        // dd(isset($validated['add_imgs']));
        // dd(!empty($validated['add_imgs']));
        // if (isset($validated['img']) && !empty($validated['img'])) {

        // }

        // dd($film);


        // есил файл загрузили в форме
        if($request->hasFile('img')){

            // инкапсулировал логику сжатия изображений в класс App/Services/ImageProcessor
            $imageProcessor = new ImageProcessor();
            //передаем само изображение, название папки куда будут сохранятся изображения, и массив с размерами изображений
            $paths = $imageProcessor->processImage($request->file('img'), 'films', [[800, 600], [300, 200]]); 

            // dd($paths);
            // $path_original = $paths[0];
            $path_medium = $paths[0];
            $path_thumbnail = $paths[1];
        //если файл не загружался и поле с путем для удаления файла пустое
        }else if(!isset($film->img) && !isset($validated['delete_img'])){
            // $path_original = $film->img;
            $path_medium = $film->img_medium;
            $path_thumbnail = $film->img_thumbnail;
        }





        // ДОПОЛНИТЕЛЬНЫЕ ИЗОБРАЖЕНИЯ =====================================
        // формируем массив из ранее загруженных дополнительных изображений
        $arr_imgs = null; // массив с дополнительными изображениями
        if(isset($film->additional_imgs)){
            $old_imgs_arr = json_decode($film->additional_imgs);
            foreach ($old_imgs_arr as $key => $img) {
                // dd($img);
                $arr_imgs[] = [
                    'image' => ['medium' => $img->image->medium, 'thumbnail' => $img->image->thumbnail],
                    'text' => $request->additional_imgs_text[$key] ?? null, //$img->text ?? null,
                    'sort' => $request->additional_imgs_sort[$key] ?? null,
                ];
            }
        }

        // если мы добавляем новые изображения или меняем какие либо из уже существующих
        if(isset($validated['additional_imgs']) && !empty($validated['additional_imgs'])) {     
            foreach ($validated['additional_imgs'] as $key => $img) {

                // инкапсулировал логику сжатия изображений в класс App/Services/ImageProcessor
                $imageProcessor = new ImageProcessor();
                //передаем само изображение, название папки куда будут сохранятся изображения, и массив с размерами изображений
                $paths = $imageProcessor->processImage($img, 'films', [[800, 600], [300, 200]]); 
                $path_add_imgs_medium = $paths[0];
                $path_add_imgs_thumbnail = $paths[1];       

                // проверяем поменяли ли мы какие либо из уже существующих изображений
                // array_key_exists - проверяем есть ли такой же ключ в json_decode($film->additional_imgs)
                if(isset($film->additional_imgs) && array_key_exists((int)$key, json_decode($film->additional_imgs))){

                    // удаляем изображения которые заменились
                    $deleteImgs = [json_decode($film->additional_imgs)[$key]->image->medium, json_decode($film->additional_imgs)[$key]->image->thumbnail];
                    Film::deleteFiles($deleteImgs);

                    // формируем ассоциативный массив для записи в базу
                    $arr_imgs[$key] = [
                        'image' => ['medium' => $path_add_imgs_medium, 'thumbnail' => $path_add_imgs_thumbnail],
                        'text' => $validated['additional_imgs_text'][$key] ?? null,
                        'sort' => $validated['additional_imgs_sort'][$key] ?? null,
                    ];      
                }else{
                    $arr_imgs[] = [
                        'image' => ['medium' => $path_add_imgs_medium, 'thumbnail' => $path_add_imgs_thumbnail],
                        'text' => $validated['additional_imgs_text'][$key] ?? null,
                        'sort' => $validated['additional_imgs_sort'][$key] ?? null,

                    ];  
                }
    
            }
        }


        // удаление дополнителных изображений если кликнули на удалить
        if(isset($request->delete_additional_img) && is_array($arr_imgs)){
            $delete_arr_imgs = explode(",", $request->delete_additional_img);
            foreach($delete_arr_imgs as $key => $elem){
                // dump((int)$elem);
                if(array_key_exists((int)$elem, $arr_imgs)){
                    // dd($arr_imgs[(int)$elem]['image']['medium']);
                    Film::deleteFiles([$arr_imgs[(int)$elem]['image']['medium'], $arr_imgs[(int)$elem]['image']['thumbnail']]);
                    unset($arr_imgs[(int)$elem]);
                }
            }
        }



        // сортируем по sort
        // $arr_imgs_sort = collect($arr_imgs)->sortBy('sort');
        $arr_imgs_sort = collect($arr_imgs)->sortBy(function($item) {
            // Если ключ 'sort' существует, вернем его значение
            // Если ключа 'sort' нет, вернем значение, которое будет отсортировано в конец (например, INF или null)
            return isset($item['sort']) ? $item['sort'] : INF;
        });
        $arr_imgs = $arr_imgs_sort->toArray();
        // сбрасываем ключи массива (индексы) благодаря этому они становятся числовыми
        $arr_imgs = array_values($arr_imgs);

        // =====================================================================

          


        $validArray = [
            'title' => $validated['title'],
            'slug' =>  $validated['slug'],
            // 'img' => $path_original ?? null, //$validated['img'],
            'img_medium' => $path_medium ?? null, //$validated['img'],
            'img_thumbnail' => $path_thumbnail ?? null, //$validated['img'],
            'additional_imgs' => json_encode($arr_imgs) ?? null,
            'iframe_video' => $validated['iframe_video'] ?? null,
            'description' => $validated['description'] ?? null,
            // 'category_id' => Category::query()->value('id'), //$validated['category_id'],
            'rating_imdb' => $validated['rating_imdb'] ?? null,
            'rating_kinopoisk' => $validated['rating_kinopoisk'] ?? null,
            'release' => $validated['release'] ?? null,
            'duration' => $validated['duration'] ?? null,
            'genre' => $validated['genre'] ?? null,
            'country' => $validated['country'] ?? null, ///////////////
            'budget' => $validated['budget'] ?? null, 
            'fees_usa' => $validated['fees_usa'] ?? null,
            'fees_world' => $validated['fees_world'] ?? null,
            'director' => $validated['director'] ?? null,
            'cast' => $cast ?? null, //$validated['cast'] ?? null,
            'published_at' => new Carbon($validated['published_at'] ?? null), // null значение по умолчанию, Carbon приводит дату к единому типу
            // 'published_at' => isset($validated['published_at']) ? Carbon::parse($validated['published_at']) : null,
            // 'published' => $validated['published'] ?? false, // false значение по умолчанию
        ];


        // если input с путем для удаления файла не пустой удаляем файл из хранилица
        if(isset($validated['delete_img'])){
            $imgs_paths = [/*$film->img,*/ $film->img_medium, $film->img_thumbnail];
            Film::deleteFiles($imgs_paths);
        }
        


        // если фильм создается 
        if(!$film){
            // dd(Film::create($validArray));
            $filmNew = Film::create($validArray);
        // если фильм редактируется
        }else{
            $filmNew = $film->update($validArray);
            // удаляем привязки к категориям если фильм уже существует
            DB::table('collection_film')->where('film_id', $film->id)->delete();

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

            if ($film) {
                // Здесь мы используем отношение collections() модели фильма для синхронизации коллекций. Метод sync() синхронизирует коллекцию с заданными идентификаторами. В результате, коллекции фильма будут соответствовать значениям в $collectionIds
                $film->collections()->sync($collectionIds);
            } else {
                // Здесь мы используем метод attach() для добавления коллекций к новому фильму $filmNew
                $filmNew->collections()->attach($collectionIds);
            }
        }
        return $filmNew;

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
