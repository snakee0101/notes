<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use App\Models\Note;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ChecklistController extends Controller
{

    public function store(Request $request) //Only converts text to a list
    {
        $collection = new Collection($request->checklist_data);

        $note = Note::findOrFail($request->note_id); //Checklist could be created only for existing note
        $checklist = $note->checklist()->create();

        $wrapped = $collection->map(fn($task, $index) => [ //wrap task text into array and assign it a position according to its index
            'text' => $task['text'],
            'position' => $index + 1,
            'completed' => $task['completed']
        ]);

        $checklist->tasks()->createMany($wrapped); //Save all tasks to the checklist

        return $note->fresh();
    }

    public function update(Request $request, Checklist $checklist) //updates tasks in checklist (in fact - it just replaces them)
    {
        $checklist->tasks()->delete();

        $wrapped = (new Collection($request->tasks))->map(fn($task, $index) => [ //wrap task text into array and assign it a position according to its index
            'text' => $task['text'],
            'position' => $index + 1,
            'completed' => $task['completed']
        ]);

        $checklist->tasks()->createMany($wrapped);

        return $checklist->note->fresh();
    }

    public function destroy(Checklist $checklist)
    {
        $note_id = $checklist->note->id;

        $text = $checklist->tasks->reduce(function ($accumulator, $task) {
            return $accumulator . $task->text . '<br>';
        }, '');

        $checklist->note->update(['body' => $text]);

        $checklist->tasks->each->delete();
        $checklist->delete();

        return Note::find($note_id);
    }
}
