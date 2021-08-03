<?php

namespace App\Utilities;

use App\Models\Note;

class NoteTypeDetector
{
    private Note $note;

    public function __construct(Note $note)
    {
        $this->note = $note;
    }

    public static function select(Note $note) : static
    {
        return new static($note);
    }
}
