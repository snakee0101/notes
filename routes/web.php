<?php

use App\Http\Controllers\NoteController;
use App\Http\Controllers\NoteToolbarController;
use App\Http\Controllers\TagController;
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

Route::middleware('auth')->group(function() {
    Route::resource('note', NoteController::class);
    Route::resource('tag', TagController::class);
    Route::post('/note/restore/{note}', [NoteController::class, 'restore'])->name('note.restore');

    Route::get('/', function () {
        return view('notes', [
            'notes' => Note::where('owner_id', auth()->id())->get()
        ]);
    })->name('notes');

    Route::get('/reminders', function () {
        return view('reminders', [
            "notes" => [] //Note::all()  //TODO: filter notes with reminders only
        ]);
    })->name('reminders');

    Route::get('/archive', function () {
        return view('archive', [
            "notes" => Note::onlyArchived()->get()
        ]);
    })->name('archive');

    Route::post('/archive/{note}', [NoteToolbarController::class, 'archive'])->name('archive_note');
    Route::delete('/unarchive/{note}', [NoteToolbarController::class, 'unarchive'])->name('unarchive_note');

    Route::get('/trash', [TrashController::class, 'index'])->name('trash');
    Route::delete('/trash/empty', [TrashController::class, 'empty'])->name('trash.empty');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
});

require __DIR__.'/auth.php';
Auth::routes();

