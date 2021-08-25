<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Tag;
use Illuminate\Support\Facades\Gate;

class TagOperationsController extends Controller
{
    public function toggle(Note $note, Tag $tag)
    {
        abort_if(Gate::denies('update_note_tags', [$tag, $note]), 403, 'Only owner and collaborators can manipulate checklists');

        $note->tags()->toggle($tag->id);
        $note->push();
    }

    public function addToNote(Note $note, Tag $tag)
    {
        abort_if(Gate::denies('update_note_tags', [$tag, $note]), 403, 'Only owner and collaborators can manipulate checklists');

        $note->tags()->attach($tag);
    }

    public function detach(Note $note, Tag $tag)
    {
        $note->tags()->detach($tag);
    }
}
