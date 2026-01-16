<?php

namespace App\Services;

// для того что бы установить фасад Image необходимо:
// 1. выполнить composer require intervention/image-laravel
// 2. выполнить php artisan vendor:publish --provider="Intervention\Image\Laravel\ServiceProvider" это добавит файл с настройками config/image.php 
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Exception\NotReadableImageException;
use Intervention\Image\Exception\NotSupportedException;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class ImageProcessor
{
    /**
     * Класс создает еще 2 изображения (миниатюру и среднего размера)
     * 
     * @param UploadedFile $image Загруженный файл изображения
     * @param string $path Путь к директории для сохранения
     * @param array $arraySizes Массив размеров [[width1, height1], [width2, height2]]
     * @return array|null Массив с путями [medium, thumbnail] или null при ошибке
     * @throws \InvalidArgumentException При невалидных параметрах
     */
    public function processImage($image, $path, $arraySizes): ?array
    {
        try {
            // Валидация входных параметров
            $this->validateInputs($image, $path, $arraySizes);

            // Создаем уникальное имя для нашего изображения
            $imageName = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();

            // Проверяем и создаем директории если их нет
            $this->ensureDirectoriesExist($path);

            // Используем фасад Image для работы с изображениями
            $originalImage = Image::read($image);
            $originalImage2 = clone $originalImage;

            // Изменение размеров изображения для среднего размера
            $mediumImage = $originalImage->cover($arraySizes[0][0], $arraySizes[0][1]);
            $mediumPath = public_path('storage/' . $path . '/medium/' . $imageName);
            $mediumImage->save($mediumPath);

            // Изменение размеров изображения для миниатюры
            $thumbImage = $originalImage2->cover($arraySizes[1][0], $arraySizes[1][1]);
            $thumbPath = public_path('storage/' . $path . '/thumbnail/' . $imageName);
            $thumbImage->save($thumbPath);

            // Проверяем, что файлы действительно созданы
            if (!file_exists($mediumPath) || !file_exists($thumbPath)) {
                throw new \RuntimeException('Не удалось сохранить изображения на диск');
            }

            // Создаем пути к нашим изображениям и возвращаем их
            $path_medium = 'storage/' . $path . '/medium/' . $imageName;
            $path_thumbnail = 'storage/' . $path . '/thumbnail/' . $imageName;

            return [$path_medium, $path_thumbnail];

        } catch (NotReadableImageException $e) {
            Log::error('ImageProcessor: Не удалось прочитать изображение', [
                'file' => $image->getClientOriginalName(),
                'error' => $e->getMessage(),
                'path' => $path
            ]);
            throw new \InvalidArgumentException('Файл не является валидным изображением: ' . $e->getMessage(), 0, $e);

        } catch (NotSupportedException $e) {
            Log::error('ImageProcessor: Формат изображения не поддерживается', [
                'file' => $image->getClientOriginalName(),
                'extension' => $image->getClientOriginalExtension(),
                'error' => $e->getMessage(),
                'path' => $path
            ]);
            throw new \InvalidArgumentException('Формат изображения не поддерживается: ' . $e->getMessage(), 0, $e);

        } catch (\InvalidArgumentException $e) {
            // Пробрасываем InvalidArgumentException дальше
            Log::error('ImageProcessor: Ошибка валидации', [
                'error' => $e->getMessage(),
                'path' => $path
            ]);
            throw $e;

        } catch (\Exception $e) {
            Log::error('ImageProcessor: Неожиданная ошибка при обработке изображения', [
                'file' => $image->getClientOriginalName(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'path' => $path
            ]);
            throw new \RuntimeException('Ошибка при обработке изображения: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Валидация входных параметров
     * 
     * @param mixed $image
     * @param string $path
     * @param array $arraySizes
     * @throws \InvalidArgumentException
     */
    private function validateInputs($image, $path, $arraySizes): void
    {
        if (!$image instanceof UploadedFile) {
            throw new \InvalidArgumentException('Параметр $image должен быть экземпляром UploadedFile');
        }

        if (empty($path) || !is_string($path)) {
            throw new \InvalidArgumentException('Параметр $path должен быть непустой строкой');
        }

        if (!is_array($arraySizes) || count($arraySizes) !== 2) {
            throw new \InvalidArgumentException('Параметр $arraySizes должен быть массивом с двумя элементами');
        }

        foreach ($arraySizes as $index => $size) {
            if (!is_array($size) || count($size) !== 2) {
                throw new \InvalidArgumentException("Элемент $index массива \$arraySizes должен содержать [width, height]");
            }

            if (!is_numeric($size[0]) || !is_numeric($size[1]) || $size[0] <= 0 || $size[1] <= 0) {
                throw new \InvalidArgumentException("Размеры изображения должны быть положительными числами");
            }
        }
    }

    /**
     * Проверяет существование директорий и создает их при необходимости
     * 
     * @param string $path
     * @throws \RuntimeException
     */
    private function ensureDirectoriesExist(string $path): void
    {
        $mediumDir = public_path('storage/' . $path . '/medium');
        $thumbnailDir = public_path('storage/' . $path . '/thumbnail');

        $directories = [$mediumDir, $thumbnailDir];

        foreach ($directories as $directory) {
            if (!file_exists($directory)) {
                if (!mkdir($directory, 0755, true)) {
                    throw new \RuntimeException("Не удалось создать директорию: {$directory}");
                }
            } elseif (!is_dir($directory)) {
                throw new \RuntimeException("Путь существует, но не является директорией: {$directory}");
            } elseif (!is_writable($directory)) {
                throw new \RuntimeException("Директория не доступна для записи: {$directory}");
            }
        }
    }
}
