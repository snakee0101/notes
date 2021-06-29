<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        return auth()->user()->tags()->pluck('name');  //returns tag names
    }

    public function store(Request $request)
    {
        Tag::create([
            'name' => $request->input('tag_name'),
            'user_id' => auth()->id()
        ]);
    }

    public function show(Tag $tag)
    {
        $data = [
            'pinned_notes' => $tag->notes()
                ->where('owner_id', auth()->id())
                ->where('pinned', true)
                ->paginate(),
            'other_notes' => $tag->notes()
                ->where('owner_id', auth()->id())
                ->where('pinned', false)
                ->paginate(),
            'tag_name' => $tag->name
        ];

        if(! request()->wantsJson()) {
            $data['pinned_notes'] = $data['pinned_notes']->toJson();
            $data['other_notes'] = $data['other_notes']->toJson();

            return view('tag', $data);
        }

        return $data[ request('notes_type') ];
    }

    public function update(Request $request, Tag $tag)
    {
        $tag->update([ 'name' => $request->input('new_name') ]);
    }

    public function destroy(Tag $tag)
    {
       return $tag->delete();
    }

    public function toggle(Note $note, Tag $tag)    //TODO: Only owner of note and tags could toggle tags
    {
        $note->tags()->toggle($tag->id); //Tag is resolved by $name property
        $note->push();
    }

    public function addToNote(Note $note, Tag $tag) //TODO: for future - only owner of note and tags could add tag to the note
    {
        $note->tags()->attach($tag);
    }

    public function removeFromNote(Note $note, Tag $tag) //TODO: for future - only owner of note and tags could remove tag from the note
    {
        $note->tags()->detach($tag);
    }
}
