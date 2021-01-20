<?php

namespace App\Http\Controllers;

use App\Models\Note;
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

        Note::create($data);
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
     * @param  \App\Models\Note  $noteId
     * @return \Illuminate\Http\Response
     */
    public function destroy($noteId)
    {
        $note = Note::withTrashed()->find($noteId);

        ($note->deleted_at) ? $note->forceDelete() : $note->delete();
    }

    public function restore($id)
    {
        Note::onlyTrashed()->find($id)
                           ->restore();
    }
}
