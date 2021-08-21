<?php

namespace App\Actions;

use Illuminate\Http\Request;

class NoteIndexAction
{
    public function getNotes(Request $request, $view, $notes)
    {
        $data = [  //get the paginators for both pinned and other notes
            'pinned_notes' => (clone $notes)->where('pinned', true)->paginate(), //clone is needed, because the index could be retrieved only once
            'other_notes' => $notes->where('pinned', false)->paginate()
        ];

        if(! $request->wantsJson()) { //if the request was not posted by axios - return view with the JSON, encoded to string
            return view($view, [
                'pinned_notes' => $data['pinned_notes']->toJson(),
                'other_notes' => $data['other_notes']->toJson()
            ]); //JSON string is passed to Vue component by component property
        }

        return $data[ $request->notes_type ]; //else - return the data itself (JSON Object) for axios, that automatically converts it to object literal
    }
}
