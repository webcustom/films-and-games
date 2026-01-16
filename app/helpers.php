<?php

// для того что бы файл helpers.php работал необходимо подключить его в composer.json
// "autoload": {
//     "files": [
//         "app/helpers.php"
//     ]
// },
// после чего перезапустить проект или выполнить в терминале  composer dump-autoload


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

if(!function_exists('active_link')){
    function active_link(string $name): string {
        return Route::is($name) ? '_active' : '';
        
    }
}


if(!function_exists('alert')){
    function alert(string $value, string $type = ''){
        session(['alert' => $value, 'alert_class' => $type]); //создаем сессию с двумя параметрами alert и alert_class
    }
}


// if(!function_exists('validate')){
//     function validate(array $attributes, array $rules): array{
//         return validator($attributes, $rules)->validate();    
//     }
// }


// if(!function_exists('isAuth')){
//     function isAuth(){
//         if (Auth::check()) {
//             // Пользователь аутентифицирован
//             return true;
//         } else {
//             // Пользователь не аутентифицирован
//             return false;
//         }
//     }
// }


if(!function_exists('isAdmin')){
    // function isAdmin(string $usertype = 'user'){
    //     if (Auth::check()) {
    //         // Пользователь аутентифицирован
    //         if($usertype === 'admin'){
    //             // пользователь админ
    //             return true;
    //         }
    //     } else {
    //         // Пользователь не аутентифицирован
    //         return false;
    //     }
    // }

    function isAdmin(): bool {
        return Auth::check() && Auth::user()->usertype === 'admin';
    }
}


