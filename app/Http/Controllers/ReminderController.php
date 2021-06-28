<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function index()
    {
        $data = [
            'pinned_notes' => auth()->user()->notes()->has('reminder')->where('pinned', true)->paginate(),
            'other_notes' => auth()->user()->notes()->has('reminder')->where('pinned', false)->paginate()
        ];

        if(! request()->wantsJson()) {
            $data['pinned_notes'] = $data['pinned_notes']->toJson();
            $data['other_notes'] = $data['other_notes']->toJson();

            return view('reminders', $data);
        }

        return $data[ request('notes_type') ];
    }

    public function store(Request $request, Note $note)
    {
        $note->reminder()->updateOrCreate([
            'note_id' => $note->id,
        ], [
            'time' => request('time'),
            'repeat' => json_decode( request('repeat') )
        ]);
    }

    public function show(Note $note)
    {
        return $note->reminder;
    }

    public function destroy(Note $note)
    {
        $note->reminder()->delete();
    }
}
