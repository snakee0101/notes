<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\Note;
use App\Models\Reminder;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->only(['header', 'body', 'pinned', 'archived', 'color', 'type']);
        $data['owner_id'] = auth()->id();

        $note = Note::create($data);

        /****Set the reminder****/
        $reminder_json = json_decode($request->reminder_json, true);

        if( !empty($reminder_json) )
        {
            $note->reminder()->create([
                'time' => $reminder_json['time'] ?? "",
                'repeat' => $reminder_json['repeat'] ?? "",
                'location' => '',
            ]);
        }

        /****Set the tags****/
        if( $request->has('tags') )
        {
            $note->tags()->attach(
                Tag::whereIn('name', $request->tags)->get()
            );
        }

        /****Set the collaborators****/
        if( $request->has('collaboratorEmails') )
        {
            $note->collaborators()->attach(
                User::whereIn('email', $request->collaboratorEmails)->get()
            );
        }

        /**Persist the links**/
        $links = Link::parseNote($note);

        foreach($links as $link)
            Link::persist($link['url'], $link['name'], $note);


        return $note;
    }

    public function show(Note $note)
    {
        $this->authorize('view', $note);
        return $note;
    }

    public function update(Request $request, Note $note)
    {
        //TODO: only owner is allowed to change an archived state
        $archivedStateChanged = request('archived') != $note->archived;
        $archivedStateChanged ? $this->authorize('updateArchived', $note)
                              : $this->authorize('update', $note);

        $note->update(
            $request->only(['header', 'body', 'pinned', 'archived', 'color', 'type'])
        );

        $links = Link::parseNote($note->fresh());

        foreach($links as $link)
            @Link::persist($link['url'], $link['name'], $note);
    }

    public function destroy(Note $note)
    {
        $this->authorize('delete', $note);
        ($note->deleted_at) ? $note->forceDelete() : $note->delete();
    }

    public function restore(Note $note)
    {
        $this->authorize('restore', $note);
        $note->restore();
    }

    public function duplicate(Note $note)
    {
        $this->authorize('duplicate', $note);
        return $note->makeCopy();
    }
}
