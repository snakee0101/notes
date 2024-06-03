<?php

use App\Actions\NoteIndexAction;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\CollaboratorController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TagOperationsController;
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
    Route::post('search', SearchController::class)->name('search');

    Route::resource('note', NoteController::class);
    Route::post('/note/restore/{note}', [NoteController::class, 'restore'])->name('note.restore');
    Route::post('/note/duplicate/{note}', [NoteController::class, 'duplicate'])->name('note.duplicate');
    Route::get('/', function (NoteIndexAction $index) {
        return $index->getNotes(request(), 'notes', auth()->user()->notes());
    })->name('notes');


    Route::resource('tag', TagController::class);
    Route::post('/toggle_tag/{note}/{tag}', [TagOperationsController::class, 'toggle'])->name('tag.toggle');
    Route::post('/tag/attach/{note}/{tag}', [TagOperationsController::class, 'attach'])->name('tag.attach');
    Route::delete('/tag/detach/{note}/{tag}', [TagOperationsController::class, 'detach'])->name('tag.detach');


    Route::resource('checklist', ChecklistController::class)->only(['store', 'update']);
    Route::post('/checklist/delete/{note}', [ChecklistController::class, 'destroy'])->name('checklist.destroy');
    Route::post('/checklist/uncheck_all/{checklist}', [ChecklistController::class, 'uncheck_all'])->name('checklist.uncheck_all');
    Route::post('/checklist/remove_completed/{checklist}', [ChecklistController::class, 'remove_completed'])->name('checklist.remove_completed');


    Route::resource('image', ImageController::class)->except('destroy');
    Route::post('/image/delete/{image}', [ImageController::class, 'destroy'])->name('image.destroy');
    Route::put('/image/restore/{image_id}', [ImageController::class, 'restore'])->name('image.restore');

    Route::post('/reminder/{note}', [ReminderController::class, 'store'])->name('reminder.store');
    Route::delete('/reminder/{note}', [ReminderController::class, 'destroy'])->name('reminder.destroy');
    Route::get('/reminder', [ReminderController::class, 'index'])->name('reminder.index');


    Route::get('/collaborator', [CollaboratorController::class, 'index'])->name('collaborator.index');
    Route::post('/collaborator/{note}', [CollaboratorController::class, 'sync'])->name('sync_collaborator');
    Route::get('/collaborator/{email}', [CollaboratorController::class, 'check'])->name('check_user_existence');


    Route::delete('/link/{link}', [LinkController::class, 'destroy'])->name('link.destroy');
    Route::post('/link/{link}/restore', [LinkController::class, 'restore'])->name('link.restore');


    Route::get('/archive', function (NoteIndexAction $index) {
        return $index->getNotes(request(), 'archive', auth()->user()->notes()->onlyArchived());
    })->name('archive');


    Route::get('/trash', [TrashController::class, 'index'])->name('trash');
    Route::delete('/trash/empty', [TrashController::class, 'empty'])->name('trash.empty');
});

Route::get('/logout', function() {
    auth()->logout();
});

require __DIR__.'/auth.php';
Auth::routes();

