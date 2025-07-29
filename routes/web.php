<?php

use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\CollectionsController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;


use App\Mail\VerifyEmail; // Убедитесь, что путь правильный
use App\Models\User;
use Illuminate\Support\Facades\Mail;

// Route::get('/', function () {
//     return view('welcome');
// });


// Route::view('/', 'home.index')->name('home'); //home-название каталога, index-название файла
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/collections/{collection}', [CollectionsController::class, 'show'])->name('collections.show');
Route::get('/categories/{category}', [CategoriesController::class, 'show'])->name('categories.show');



//что бы применить один middleware на группу роутов надо сделать так:
Route::middleware('guest')->group(function(){ //guest проверяет пользователь гость или нет
    // разкоментить /register для добавления нового пользователя
    // Route::get('/register', [RegisterController::class, 'index'])->name('register.index');
    // Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
    Route::get('verify-email/{token}/{pendingUserId}', [RegisterController::class, 'verifyEmail'])->name('register.verify-email');

    Route::get('/login', [LoginController::class, 'index'])->name('login.index');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

});

Route::get('/logout', [LoginController::class, 'logout'])->name('logout.index');



// тут мы можем сразу передать pendingUser в шаблон таким способом
Route::get('/verify-email/{pendingUserId}', function ($pendingUserId) {
    return view('register.verify-email', ['pendingUserId' => $pendingUserId]);
})->name('verification.notice');



// Роуты для верификации

// отправляем на страницу верификации (уведомление о проверке)
// Route::get('/verify-email', function () {
//     return view('register.verify-email');
//     // return view('register.verify-email'); //создаем страницу
// })->middleware('auth')->name('verification.notice');

// обрабатываем проверку (переход с кнопки в письме на почте)
// Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
//     $request->fulfill();
//     return redirect('/admin/dashboard');
// })->middleware(['auth', 'signed'])->name('verification.verify');

// повторная верификация
// Route::post('/email/verification-notification', function (Request $request) {
//     // dd($request);
//     $request->user()->sendEmailVerificationNotification();
//     return back()->with('message', 'Verification link sent!');
// })->middleware(['auth', 'throttle:3,1'])->name('verification.send'); //throttle:3,1 - позволяет пользователю сделать 3 запроса за 1 минуту, если их будет больше то выведет ошибку

// // // после чего можем применять проверку ->middleware(['auth', 'verified'])


// повторная верификация
Route::post('/email/verification-notification/{pendingUserId}', [RegisterController::class, 'resendVerificationEmail'])->name('verification.resend');




// Route::get('/test-email', function () {
//     Mail::raw('Тестовое письмо', function ($message) {
//         $message->from('postmailer1@yandex.ru', 'WebCustom title');
//         $message->to('webcustom1@gmail.com');
//         $message->subject('Тестовое письмо');
//     });
//     return 'Письмо отправлено!';
// });