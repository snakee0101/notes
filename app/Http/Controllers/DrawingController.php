<?php

namespace App\Http\Controllers;

use App\Models\Drawing;
use App\Models\Image;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DrawingController extends Controller
{
    public function store(Request $request)
    {
        $note = Note::find( $request->input('note_id') );

        abort_if(Gate::denies('image_manipulation', $note), 403, 'Only owner and collaborators can manipulate images');

        $image = $request->file('image');

        $drawings = $note->drawings()->create(
            Drawing::processUpload($image)
        );

        $note->refresh();
        $note->searchable();

        return $drawings;
    }

    public function update(Request $request, Drawing $drawing)
    {
        $image = $request->file('image'); // files could only be sent through POST request

        $drawing->update(
            Drawing::processUpload($image)
        );

        return $drawing;
    }

    public function destroy(Drawing $drawing)
    {
        abort_if(Gate::denies('image_manipulation', $drawing->note), 403, 'Only owner and collaborators can manipulate drawings');

        $note = $drawing->note;
        $drawing->delete();
        $note->searchable();
        
        return $drawing;
    }

    public function restore($drawing_id)
    {
        $drawing = Drawing::withTrashed()->findOrFail($drawing_id);

        $drawing->restore();
        $drawing->note->searchable();
    }
}
