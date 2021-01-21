<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Utilities\Trash;
use Illuminate\Http\Request;

class TrashController extends Controller
{
    public function index() {
        return view('trash', [
            'notes' => Note::onlyTrashed()->where('owner_id', auth()->id())->get()
        ]);
    }

    public function empty() {
        Trash::empty();
    }
}
