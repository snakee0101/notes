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

        abort_if(Gate::denies('checklist', $note), 403, 'Only owner and collaborators can manipulate checklists');

        $checklist = $note->checklist()->create();

        $checklist->tasks()->createMany( Checklist::wrap($request->checklist_data) ); //Save all tasks to the checklist

        $note->refresh();
        $note->searchable();

        return $note;
    }

    public function update(Request $request, Checklist $checklist) //updates tasks in checklist (in fact - it just replaces them)
    {
        abort_if(Gate::denies('checklist', $checklist->note), 403, 'Only owner and collaborators can manipulate checklists');

        $checklist->tasks()->delete();
        $checklist->tasks()->createMany( Checklist::wrap($request->tasks) );

        $note = $checklist->note;

        $note->refresh();
        $note->searchable();

        return $note;
    }

    public function destroy(Note $note)
    {
        abort_if(Gate::denies('checklist', $note), 403, 'Only owner and collaborators can manipulate checklists');

        if($note->checklist) {
            $note->update(['body' => $note->checklist->toHTML()]);
            $note->checklist()->delete();
        }

        $note->refresh();
        $note->searchable();

        return $note;
    }

    public function uncheck_all(Checklist $checklist)
    {
        abort_if(Gate::denies('checklist', $checklist->note), 403, 'Only owner and collaborators can manipulate checklists');

        $checklist->uncheckAll();
       
        $note = $checklist->note;

        $note->refresh();
        $note->searchable();

        return $note;
    }

    public function remove_completed(Checklist $checklist)
    {
        abort_if(Gate::denies('checklist', $checklist->note), 403, 'Only owner and collaborators can manipulate checklists');

        $note = Note::find($checklist->note->id);

        $checklist->removeCompleted();
        $checklist->refresh();

        if($checklist->tasks()->count() == 0) {
            $checklist->delete();
        }

        $note->refresh();
        $note->searchable();

        return $note;
    }
}
