<?php

namespace App\Http\Controllers;

use App\Actions\NoteIndexAction;
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
        return auth()->user()->tags()->create([
            'name' => $request->tag_name
        ]);
    }

    public function show(Tag $tag, NoteIndexAction $index)
    {
        return $index->getNotes(request(), 'tag', $tag->notes()->where('owner_id', auth()->id()), [
            'tag' => $tag
        ]);
    }

    public function update(Request $request, Tag $tag)
    {
        $tag->update([ 'name' => $request->new_name ]);
    }

    public function destroy(Tag $tag)
    {
       return $tag->delete();
    }

}
