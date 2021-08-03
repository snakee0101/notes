<?php

namespace App\Utilities;

use App\Models\Note;

class NoteTypeDetector
{
    private Note $note;
    private array $types = [];

    private function __construct(Note $note)
    {
        $this->note = $note;
    }

    private function detectImages()
    {
        if($this->note->images()->exists())
            $this->types[] = 'image';
    }

    private function detectChecklist()
    {
        if($this->note->checklist()->exists())
            $this->types[] = 'checklist';
    }

    private function detectLinks()
    {
        if($this->note->links()->exists())
            $this->types[] = 'links';
    }

    public static function select(Note $note) : static
    {
        return new static($note);
    }

    public function detectTypes() : array
    {
        $this->detectImages();
        $this->detectChecklist();
        $this->detectLinks();
        return $this->types;
    }
}
