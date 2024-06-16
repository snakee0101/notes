<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Note;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function store(Request $request)
    {
        $note = Note::find( $request->input('note_id') );

        abort_if(Gate::denies('image_manipulation', $note), 403, 'Only owner and collaborators can manipulate images');

        $image = $request->file('image');

        return $note->images()->create(
            Image::processUpload($image)
        );
    }

    public function destroy(Image $image)
    {
        abort_if(Gate::denies('image_manipulation', $image->note), 403, 'Only owner and collaborators can manipulate images');

        $image_id = $image->id;

        $image->delete();
        $image->note->searchable();

        return $image_id;
    }

    public function restore($image_id)
    {
        $image = Image::withTrashed()->findOrFail($image_id);

        abort_if(Gate::denies('image_manipulation', $image->note), 403, 'Only owner and collaborators can manipulate images');

        $image->restore();

        return $image->fresh();
    }
}
