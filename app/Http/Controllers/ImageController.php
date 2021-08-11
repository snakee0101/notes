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
        $paths = Image::processUpload($image);

        return $note->images()->create([
            'image_path' => $paths['image_path'],
            'thumbnail_small_path' => $paths['thumbnail_small_path'],
            'thumbnail_large_path' => $paths['thumbnail_large_path'],
        ]);
    }

    public function destroy(Image $image)
    {
        abort_if(Gate::denies('image_manipulation', $image->note), 403, 'Only owner and collaborators can manipulate images');

        $image_id = $image->id;
        $image->delete();

        return $image_id;
    }

    public function restore($image_id)
    {
        $image = Image::withTrashed()->findOrFail($image_id);

        abort_if(Gate::denies('image_manipulation', $image->note), 403, 'Only owner and collaborators can manipulate images');

        $image->restore();

        return $image->fresh();
    }

    public function recognize(Request $request)
    {
        $image = Image::where('image_path', $request->image_path)->first();

        abort_if(Gate::denies('image_manipulation', $image->note), 403, 'Only owner and collaborators can manipulate images');

        $tesseract = new TesseractOCR(storage_path() . '/app/' . Str::after($request->image_path,'/storage'));
        return $tesseract->run();
    }
}
