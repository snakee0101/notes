<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Reminder;
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
        $reminder_json = json_decode($request->reminder_json);

        if( !is_null($reminder_json) )
        {
            $note->reminder()->create([
                'time' => $reminder_json->time,
                'repeat' => $reminder_json->repeat ?? "",
                'location' => '',
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function show(Note $note)
    {
        //
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
        return $note->makeCopy();
    }

    public function get_tags(Note $note)
    {
        return $note->tags;
    }
}
