<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('books.index');
});

Route::resource('books', BookController::class);

Route::resource('books.reviews', \App\Http\Controllers\ReviewController::class)
    ->scoped(['review' => 'book'])
    ->only('create','store');

//Route::get('/books', [BookController::class, "index"])->name('books.index');
