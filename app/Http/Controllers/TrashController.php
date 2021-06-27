<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Utilities\Trash;
use Illuminate\Http\Request;

class TrashController extends Controller
{
    public function index() {
        return view('trash', [
            'notes' => Note::withArchived()
                           ->onlyTrashed()
                           ->where('owner_id', auth()->id())
                           ->paginate()
                           ->toJson()
        ]);
    }

    public function empty() {
        Trash::empty();
    }
}
