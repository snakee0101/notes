<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Utilities\Trash;
use Illuminate\Http\Request;

class TrashController extends Controller
{
    public function index() {
        return view('trash', [
            "pinned_notes" => auth()->user()->notes()->withArchived()
            ->onlyTrashed()
            ->where('pinned', true)
            ->paginate()->toJson(),

            'other_notes' => auth()->user()->notes()->withArchived()
            ->onlyTrashed()
            ->where('pinned', false)
            ->paginate()->toJson()
        ]);
    }

    public function empty() {
        Trash::empty();
    }
}
