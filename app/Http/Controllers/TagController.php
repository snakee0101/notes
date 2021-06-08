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
        return view('tag', [
            'tag_name' => $tag->name,
            'notes' => $tag->notes()
                           ->where('owner_id', auth()->id())
                           ->get()
        ]);
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
}
