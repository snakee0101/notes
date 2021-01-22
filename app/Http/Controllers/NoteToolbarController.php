<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NoteToolbarController extends Controller
{
    public function archive(Note $note)
    {
        $note->update(['archived' => true]);
    }

    public function unarchive(Note $note)
    {
        $note->update(['archived' => false]);
    }
}
