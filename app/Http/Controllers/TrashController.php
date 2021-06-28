<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Utilities\Trash;
use Illuminate\Http\Request;

class TrashController extends Controller
{
    public function index() {
        $data = [
            'pinned_notes' => auth()->user()->notes()->withArchived()
                ->onlyTrashed()
                ->where('pinned', true)
                ->paginate(),
            'other_notes' => auth()->user()->notes()->withArchived()
                ->onlyTrashed()
                ->where('pinned', false)
                ->paginate()
        ];

        if(! request()->wantsJson()) {
            $data['pinned_notes'] = $data['pinned_notes']->toJson();
            $data['other_notes'] = $data['other_notes']->toJson();

            return view('trash', $data);
        }

        return $data[ request('notes_type') ];
    }

    public function empty() {
        Trash::empty();
    }
}
