<?php

use App\Http\Controllers\CollaboratorController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\TagController;
use App\Models\Note;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;
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
    Route::post('/toggle_tag/{note}/{tag}', [TagController::class, 'toggle'])->name('tag.toggle');
    Route::post('/note/{note}/get_tags', [NoteController::class, 'get_tags'])->name('note.get_tags');

    Route::resource('image', ImageController::class);

    Route::post('/note/restore/{note}', [NoteController::class, 'restore'])->name('note.restore');
    Route::post('/note/duplicate/{note}', [NoteController::class, 'duplicate'])->name('note.duplicate');

    Route::post('/reminder/{note}', [ReminderController::class, 'store'])->name('reminder.store');
    Route::delete('/reminder/{note}', [ReminderController::class, 'destroy'])->name('reminder.destroy');
    Route::get('/reminder', [ReminderController::class, 'index'])->name('reminder.index');
    Route::get('/reminder/{note}', [ReminderController::class, 'show'])->name('reminder.show');

    Route::post('/collaborator/{note}', [CollaboratorController::class, 'sync'])->name('sync_collaborator');
    Route::get('/collaborator/{email}', [CollaboratorController::class, 'check'])->name('check_user_existence');
    Route::get('/collaborators/{note}', [CollaboratorController::class, 'index'])->name('collaborators_list');

    Route::get('/', function () {
        $data = [  //get the paginators for both pinned and other notes
            'pinned_notes' => auth()->user()->notes()->where('pinned', true)->paginate(),
            'other_notes' => auth()->user()->notes()->where('pinned', false)->paginate()
        ];

        if(! request()->wantsJson()) { //if the request was not posted by axios - return view with the JSON, encoded to string
            $data['pinned_notes'] = $data['pinned_notes']->toJson();
            $data['other_notes'] = $data['other_notes']->toJson();

            return view('notes', $data); //JSON string is passed to Vue component by component property
        }

        return $data[ request('notes_type') ]; //else - return the data itself (JSON Object) for axios, that automatically converts it to object literal
    })->name('notes');

    Route::get('/archive', function () {
        return view('archive', [
            "pinned_notes" => auth()->user()->notes()->onlyArchived()->where('pinned', true)->paginate()->toJson(), //TODO: set user restriction
            "other_notes" =>auth()->user()->notes()->onlyArchived()->where('pinned', false)->paginate()->toJson() //TODO: set user restriction
        ]);
    })->name('archive');

    Route::delete('/detach_tag/{note}/{tag}', function(Note $note, Tag $tag){
        $note->tags()->detach($tag);
    })->name('detach_tag');

    Route::get('/trash', [TrashController::class, 'index'])->name('trash');
    Route::delete('/trash/empty', [TrashController::class, 'empty'])->name('trash.empty');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
});

require __DIR__.'/auth.php';
Auth::routes();

