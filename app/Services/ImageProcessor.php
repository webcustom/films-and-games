<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

// use Intervention\Image\Exception\NotReadableImageException;
// use Intervention\Image\Exception\NotSupportedException;

class ImageProcessor{
    // класс создает еще 2 изображения (миниатюру и среднего размера) 
    public function processImage($image, $path, $arraySizes){
        // try {
            //создаем уникальное имя для нашего изображения
            $imageName = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
            // dd($image);

            //загружаем изображение в каталог
            $image->move('storage/'.$path, $imageName);


            //изменение размеров изображения
            $imgManager = new ImageManager(new Driver());
            $thumbImage = $imgManager->read('storage/'.$path.'/'.$imageName);
            $thumbImage->cover($arraySizes[0][0], $arraySizes[0][1]);
            // сохраняем новое изображение в другом каталоге (необходимо создать каталог)
            $thumbImage->save(public_path('storage/'.$path.'/thumbnail/'.$imageName));

            // добавляем еще medium размер
            $mediumImage = $imgManager->read('storage/'.$path.'/'.$imageName);
            $mediumImage->cover($arraySizes[1][0], $arraySizes[1][1]);
            $mediumImage->save(public_path('storage/'.$path.'/medium/'.$imageName), 90); // 90 - качество изображения

            $path_original = 'storage/'.$path.'/'.$imageName;
            $path_medium = 'storage/'.$path.'/medium/'.$imageName;
            $path_thumbnail = 'storage/'.$path.'/thumbnail/'.$imageName;

            return [$path_original, $path_medium, $path_thumbnail];
        // } catch (NotReadableImageException  $e) {
        //     // Обработка ошибки, если изображение не может быть прочитано
        //     return response()->json(['error' => 'Изображение не может быть прочитано: ' . $e->getMessage()], 500);
        // } catch (NotSupportedException $e) {
        //      // Обработка ошибки, если операция над изображением не поддерживается
        //      return response()->json(['error' => 'Операция над изображением не поддерживается: ' . $e->getMessage()], 500);
        // } catch (\Exception $e) {
        //     // Обработка других исключений
        //     return null;
        // }
    }
}
