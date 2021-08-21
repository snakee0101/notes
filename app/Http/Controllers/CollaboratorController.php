<?php

namespace App\Http\Controllers;

use App\Actions\NoteIndexAction;
use App\Models\Note;
use App\Models\User;
use App\Notifications\CollaboratorWasAddedNotification;
use App\Notifications\CollaboratorWasDeletedNotification;
use Illuminate\Support\Facades\Gate;

class CollaboratorController extends Controller
{
    public function index(NoteIndexAction $index)
    {
        return $index->getNotes(request(), 'collaborator_notes', auth()->user()->collaboratorNotes());
    }

    public function sync(Note $note)
    {
        abort_if(Gate::denies('sync_collaborator', $note), 403, 'Only owner of the note may update collaborators');

        $res = $note->collaborators()->sync( request('collaborator_ids') );

        foreach($res['attached'] as $attachedCollaboratorId)
        {
           User::find($attachedCollaboratorId)->notify( new CollaboratorWasAddedNotification($note) );
        }

        foreach($res['detached'] as $detachedCollaboratorId)
        {
            User::find($detachedCollaboratorId)->notify( new CollaboratorWasDeletedNotification($note) );
        }

        return $note->fresh()->collaborators;
    }

    public function check($email) //returns user object, if it exists
    {
        $data = [ 'exists' => User::whereEmail($email)->exists() ];
        if($data['exists'])
            $data['user'] = User::firstwhere('email', $email);

        return $data;
    }
}
