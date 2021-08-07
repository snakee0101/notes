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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function show(Note $note)
    {
        return $note;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function edit(Note $note)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Note $note)
    {
        $note->update(
            $request->only(['header', 'body', 'pinned', 'archived', 'color', 'type'])
        );

        $links = Link::parseNote($note->fresh());

        foreach($links as $link)
            @Link::persist($link['url'], $link['name'], $note);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function destroy(Note $note)
    {
        ($note->deleted_at) ? $note->forceDelete() : $note->delete();
    }

    public function restore(Note $note)
    {
        $note->restore();
    }

    public function duplicate(Note $note)
    {
        $this->authorize('duplicate', $note);
        return $note->makeCopy();
    }

    public function get_tags(Note $note)
    {
        return $note->tags;
    }
}
