<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('notes');
})->name('notes');

Route::get('/reminders', function () {
    return view('reminders');
})->name('reminders');

Route::get('/archive', function () {
    return view('archive');
})->name('archive');

Route::get('/trash', function () {
    return view('trash');
})->name('trash');

Route::get('/tag/{tag}', function ($tag) {
    return view('tag');
})->name('tag');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
Auth::routes();

Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

