<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function index()
    {
        return view('reminders', [
            "notes" => Note::has('reminder')->get()
        ]);
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
