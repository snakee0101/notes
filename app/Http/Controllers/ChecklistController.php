<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ChecklistController extends Controller
{

    public function store(Request $request) //Only converts text to a list
    {
        $task_content_array = Checklist::parse($request->checklist_data);
        $collection = new Collection($task_content_array);

        $note = Note::findOrFail($request->note_id); //Checklist could be created only for existing note
        $checklist = $note->checklist()->create();

        $wrapped = $collection->map(fn($item, $index) => [ //wrap task text into array and assign it a position according to its index
            'text' => $item,
            'position' => $index + 1
        ]);

        $checklist->tasks()->createMany($wrapped); //Save all tasks to the checklist
    }

    public function update(Request $request, Checklist $checklist)
    {

    }

    public function destroy(Checklist $checklist)
    {

    }
}
