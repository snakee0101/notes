<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ChecklistController extends Controller
{

    public function store(Request $request) //Only converts text to a list
    {
        $note = Note::findOrFail($request->note_id); //Checklist could be created only for existing note

        if(Gate::denies('checklist', $note))
            return response('Only owner and collaborators can manipulate checklists', 403);

        $checklist = $note->checklist()->create();

        $checklist->tasks()->createMany( Checklist::wrap($request->checklist_data) ); //Save all tasks to the checklist

        return $note->fresh();
    }

    public function update(Request $request, Checklist $checklist) //updates tasks in checklist (in fact - it just replaces them)
    {
        if(Gate::denies('checklist', $checklist->note))
            return response('Only owner and collaborators can manipulate checklists', 403);

        $checklist->tasks()->delete();
        $checklist->tasks()->createMany( Checklist::wrap($request->tasks) );

        return $checklist->note->fresh();
    }

    public function destroy(Note $note)
    {
        if(Gate::denies('checklist', $note))
            return response('Only owner and collaborators can manipulate checklists', 403);

        $note->update(['body' => $note->checklist->toHTML()]);
        $note->checklist()->delete();

        return $note->fresh();
    }

    public function uncheck_all(Checklist $checklist)
    {
        if(Gate::denies('checklist', $checklist->note))
            return response('Only owner and collaborators can manipulate checklists', 403);

        $checklist->uncheckAll();
        return $checklist->note;
    }

    public function remove_completed(Checklist $checklist)
    {
        if(Gate::denies('checklist', $checklist->note))
            return response('Only owner and collaborators can manipulate checklists', 403);

        $checklist->removeCompleted();
        return Note::find($checklist->note->id);
    }
}
