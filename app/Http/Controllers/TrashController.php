<?php

namespace App\Http\Controllers;

use App\Actions\NoteIndexAction;
use App\Utilities\Trash;

class TrashController extends Controller
{
    public function index(NoteIndexAction $index) {
       return $index->getNotes(request(), 'trash', auth()->user()->notes()->withArchived()
           ->onlyTrashed());
    }

    public function empty() {
        Trash::empty();
    }
}
