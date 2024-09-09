<?php



use App\Http\Controllers\Admin\FilmsController;
use App\Http\Controllers\Admin\CollectionsController;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\GamesController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });





// основная страница если пользователь аутентифицирован ("/admin" - такой путь предопределенные и будет конфликтовать если используем встроенную систему аутентификации )
Route::view('/admin/dashboard', 'admin.index')->middleware(AdminMiddleware::class)->name('admin.index');

Route::prefix('admin')->middleware(AdminMiddleware::class)->group(function(){
    Route::get('/films', [FilmsController::class, 'index'])->name('admin.films.index');
    Route::get('/films/create', [FilmsController::class, 'create'])->name('admin.films.create');
    Route::post('/films/store', [FilmsController::class, 'store'])->name('admin.films.store');
    // Route::get('/films/{film}', [FilmsController::class, 'show'])->name('admin.films.show');
    Route::get('/films/{film}/edit', [FilmsController::class, 'edit'])->name('admin.films.edit');
    Route::put('/films/{film}/update', [FilmsController::class, 'update'])->name('admin.films.update');
    Route::delete('/films/delete', [FilmsController::class, 'delete'])->name('admin.films.delete');


    Route::get('/games', [GamesController::class, 'index'])->name('admin.games.index');
    Route::get('/games/create', [GamesController::class, 'create'])->name('admin.games.create'); 
    Route::post('/games/store', [GamesController::class, 'store'])->name('admin.games.store');
    Route::get('/games/{game}/edit', [GamesController::class, 'edit'])->name('admin.games.edit');
    Route::put('/games/{game}/update', [GamesController::class, 'update'])->name('admin.games.update');
    Route::delete('/games/delete', [GamesController::class, 'delete'])->name('admin.games.delete');


    Route::get('/collections', [CollectionsController::class, 'index'])->name('admin.collections.index');
    Route::get('/collections/create', [CollectionsController::class, 'create'])->name('admin.collections.create');
    Route::post('/collections/store', [CollectionsController::class, 'store'])->name('admin.collections.store');
    // Route::get('/posts/{post}', [CollectionsController::class, 'show'])->name('admin.collections.show');
    Route::get('/collections/{collection}/edit', [CollectionsController::class, 'edit'])->name('admin.collections.edit');
    Route::put('/collections/{collection}/update', [CollectionsController::class, 'update'])->name('admin.collections.update');
    Route::delete('/collections/delete', [CollectionsController::class, 'delete'])->name('admin.collections.delete');


    Route::get('/categories', [CategoriesController::class, 'index'])->name('admin.categories.index');
    Route::get('/categories/create', [CategoriesController::class, 'create'])->name('admin.categories.create');
    Route::post('/categories/store', [CategoriesController::class, 'store'])->name('admin.categories.store');
    // Route::get('/posts/{post}', [CollectionsController::class, 'show'])->name('admin.collections.show');
    Route::get('/categories/{category}/edit', [CategoriesController::class, 'edit'])->name('admin.categories.edit');
    Route::put('/categories/{category}/update', [CategoriesController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/delete', [CategoriesController::class, 'delete'])->name('admin.categories.delete');


    Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings.index');
    Route::put('/settings/{user}/update', [SettingsController::class, 'update'])->name('admin.settings.update');



});



