<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class CollaboratorController extends Controller
{
    public function index(Request $request)
    {
        $data = [  //get the paginators for both pinned and other notes
            'pinned_notes' => auth()->user()->collaboratorNotes()->where('pinned', true)->paginate(),
            'other_notes' => auth()->user()->collaboratorNotes()->where('pinned', false)->paginate()
        ];

        if(! request()->wantsJson()) { //if the request was not posted by axios - return view with the JSON, encoded to string
            $data['pinned_notes'] = $data['pinned_notes']->toJson();
            $data['other_notes'] = $data['other_notes']->toJson();

            return view('collaborator_notes', $data); //JSON string is passed to Vue component by component property
        }

        return $data[ request('notes_type') ];
    }

    public function sync(Note $note)
    {
        if(Gate::denies('sync_collaborator', $note))
            return response('Only owner of the note may update collaborators', 403);

        $emails = request('emails');

        $note->collaborators()->sync(
            User::whereIn('email', $emails)->pluck('id')
        );

        return $note->collaborators->pluck('email');
        //TODO: Send mail to the user when it is added or deleted from collaborators
    }

    public function check($email)
    {
        return [ 'exists' => User::whereEmail($email)->exists() ];
    }
}
