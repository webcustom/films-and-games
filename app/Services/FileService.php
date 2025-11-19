<?php

namespace  App\Services;

class FileService
{
    public static function deleteFiles(array $paths): void
    {
        foreach ($paths as $path) {
            if ($path && file_exists(public_path($path))) {
                unlink(public_path($path));
            }
        }
    }
}