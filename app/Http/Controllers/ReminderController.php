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
        //
    }

    public function update(Request $request, Note $note)
    {
        //
    }

    public function destroy(Note $note)
    {
        $note->reminder()->delete();
    }
}
