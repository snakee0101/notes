<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tag;
use App\Models\Link;
use App\Models\Note;
use App\Models\User;
use App\Models\Image;
use App\Models\Drawing;
use App\Models\Reminder;
use App\Models\Checklist;
use Illuminate\Http\Request;
use App\Notifications\CollaboratorWasAddedNotification;

class NoteController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->only(['header', 'body', 'color', 'type']);
        $data['archived'] = $request->boolean('archived');
        $data['pinned'] = $request->boolean('pinned');
        $data['owner_id'] = auth()->id();

        $note = Note::create($data);

        /****Set the reminder****/
        $reminder = json_decode($request->input('reminder'));

        if (empty(get_object_vars($reminder)) == false) {
            $note->reminders()->create([
                'user_id' => auth()->id(),
                'time' => is_null($reminder->time) ? null : Carbon::parse($reminder->time),
                'repeat' => isset($reminder->repeat) ? json_decode($reminder->repeat) : null,
                'location' => '',
            ]);
        }

        $note->tags()->attach( json_decode(request('tag_ids')) );  /****Set the tags****/
        $note->collaborators()->sync( json_decode(request('collaborator_ids')) );  /****Set the collaborators****/

        $note->collaborators->each->notify( new CollaboratorWasAddedNotification($note) );

        /**Persist the links**/
        $links = Link::parseNote($note);

        foreach($links as $link)
            Link::persist($link['url'], $link['name'], $note);
           
        /**Persist checklists */
        $checklist_data = json_decode($request->checklist_data);

        if(! empty($checklist_data) ) {
            $checklist = $note->checklist()->create();
            $checklist->tasks()->createMany( Checklist::wrap($request->checklist_data) );
        }

        /**Persist images**/
        foreach($request->file('images') as $image) {
            $note->images()->create( Image::processUpload($image) );
        }

        /**Persist drawings**/
        foreach($request->file('drawings') as $drawing) {
            $note->drawings()->create( Drawing::processUpload($drawing) );
        }

        $note->refresh();
        $note->searchable();

        $note->load('tags', 'drawings');

        return $note;
    }

    public function show(Note $note)
    {
        $this->authorize('view', $note);
        return $note;
    }

    public function update(Request $request, Note $note)
    {
        $archivedStateChanged = request('archived') != $note->archived;
        $archivedStateChanged ? $this->authorize('updateArchived', $note)
                              : $this->authorize('update', $note);

        $note->update(
            $request->only(['header', 'body', 'pinned', 'archived', 'color', 'type'])
        );

        $links = Link::parseNote($note = $note->fresh());

        foreach($links as $link)
            @Link::persist($link['url'], $link['name'], $note);

        $note->searchable();
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
