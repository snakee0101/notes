<?php

namespace App\Http\Controllers;

use App\Models\Drawing;
use App\Models\Image;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DrawingController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $note = Note::find( $request->input('note_id') );

        abort_if(Gate::denies('image_manipulation', $note), 403, 'Only owner and collaborators can manipulate images');

        $image = $request->file('image');

        return $note->drawings()->create(
            Drawing::processUpload($image)
        );
    }

    public function show(Drawing $drawing)
    {
        //
    }

    public function edit(Drawing $drawing)
    {
        //
    }

    public function update(Request $request, Drawing $drawing)
    {
        $image = $request->file('image'); // files could only be sent through POST request

        return $drawing->update(
            Drawing::processUpload($image)
        );
    }

    public function destroy(Drawing $drawing)
    {
        //
    }
}
