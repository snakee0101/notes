<?php

namespace App\Utilities;

use App\Models\Note;

class NoteTypeDetector
{
    private Note $note;
    private array $types = [];

    public function __construct(Note $note)
    {
        $this->note = $note;
    }

    public static function select(Note $note) : static
    {
        return new static($note);
    }

    public function detectImages()
    {
        if($this->note->images()->exists())
            $this->types[] = 'image';
    }

    public function detectTypes() : array
    {
        $this->detectImages();
        return $this->types;
    }
}
