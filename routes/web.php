<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PokemonController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/** Index */
// Route::get('/', function () {
//     return view('welcome');
// });

/** Index */
Route::get('/', [HomeController::class, 'index']);

/** User Authentication */
Auth::routes();

/** User Dashboard */
Route::get('/home', [HomeController::class, 'index'])->name('home');

/** User */
Route::resource('user', UserController::class)
    ->only(['edit', 'update']);

/** Pokemon */
Route::prefix('pokemon')->name('pokemon.')->group(function () {
    Route::get('', [PokemonController::class, 'index'])
        ->name('index');
    Route::post('favorite', [PokemonController::class, 'favorite'])
        ->name('favorite');
    Route::delete('{pokemon}/unfavorite', [PokemonController::class, 'unfavorite'])
        ->name('unfavorite');
    Route::post('like', [PokemonController::class, 'like'])
        ->name('like');
    Route::delete('{pokemon}/unlike', [PokemonController::class, 'unlike'])
        ->name('unlike');
    Route::post('hate', [PokemonController::class, 'hate'])
        ->name('hate');
    Route::delete('{pokemon}/unhate', [PokemonController::class, 'unhate'])
        ->name('unhate');
});
