<?php

namespace App\Services;

// для того что бы установить фасад Image необходимо:
// 1. выполнить composer require intervention/image-laravel
// 2. выполнить php artisan vendor:publish --provider="Intervention\Image\Laravel\ServiceProvider" это добавит файл с настройками config/image.php 
use Intervention\Image\Laravel\Facades\Image;


use Intervention\Image\Exception\NotReadableImageException;
use Intervention\Image\Exception\NotSupportedException;

class ImageProcessor{
    // класс создает еще 2 изображения (миниатюру и среднего размера) 
    public function processImage($image, $path, $arraySizes){
        try {
            
            //создаем уникальное имя для нашего изображения
            $imageName = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();

            // используем фасад Image для работы с изображениями 
            $originalImage = Image::read($image);
            $originalImage2 = clone $originalImage;


            // dd($image);

            //изменение размеров изображения
            $mediumImage = $originalImage->cover($arraySizes[0][0], $arraySizes[0][1]);
            // сохраняем новое изображение в другом каталоге (необходимо создать каталог)
            $mediumImage->save(public_path('storage/'.$path.'/medium/'.$imageName));

            // тоже самое делаем для миниатюры
            $thumbImage = $originalImage2->cover($arraySizes[1][0], $arraySizes[1][1]);
            $thumbImage->save(public_path('storage/'.$path.'/thumbnail/'.$imageName));


            // создаем пути к нашим изображениям и возвращаем их
            $path_medium = 'storage/'.$path.'/medium/'.$imageName;
            $path_thumbnail = 'storage/'.$path.'/thumbnail/'.$imageName;

            return [$path_medium, $path_thumbnail];
            
        } catch (\Exception $e) {
            // Обработка других исключений
            return null;
        }
    }
}
