<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TagController extends Controller
{
    public function index()
    {
        return auth()->user()->tags;
    }

    public function store(Request $request)
    {
        auth()->user()->tags()->create([
            'name' => $request->tag_name
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
            'tag' => $tag
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
        $tag->update([ 'name' => $request->new_name ]);
    }

    public function destroy(Tag $tag)
    {
       return $tag->delete();
    }

    public function toggle(Note $note, Tag $tag)
    {
        $note->tags()->toggle($tag->id); //Tag is resolved by $name property
        $note->push();
    }

    public function addToNote(Note $note, Tag $tag)
    {
        if(Gate::denies('update_tags', $tag))
            return response('Only owner of the note may update collaborators', 403);

        $note->tags()->attach($tag);
    }

    public function removeFromNote(Note $note, Tag $tag)
    {
        $note->tags()->detach($tag);
    }
}
