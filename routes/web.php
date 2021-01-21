<?php

use App\Http\Controllers\NoteController;
use App\Models\Note;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrashController;

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

Route::resource('note', NoteController::class);
Route::post('note/restore/{id}', [NoteController::class, 'restore'])->name('note.restore');

Route::get('/', function () {
    return view('notes', [
        'notes' => Note::where('owner_id', auth()->id())->get()
    ]);
})->name('notes');

Route::get('/reminders', function () {
    return view('reminders');
})->name('reminders');

Route::get('/archive', function () {
    return view('archive');
})->name('archive');

Route::get('/trash', [TrashController::class, 'index'])->name('trash');
Route::delete('/trash/empty', [TrashController::class, 'empty'])->name('trash.empty');

Route::get('/tag/{tag}', function ($tag) {
    return view('tag');
})->name('tag');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
Auth::routes();

Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

