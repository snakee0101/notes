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
    public function __construct(Request $request)
    {


    }

    public function store(Request $request)
    {
        $note = Note::find( $request->input('note_id') );

        if(Gate::denies('image_manipulation', $note))
            return response('Only owner and collaborators can manipulate images', 403);

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
        $image_id = $image->id;
        $image->delete();

        return $image_id;
    }

    public function restore($image_id)
    {
        $image = Image::withTrashed()->findOrFail($image_id);
        $image->restore();

        return $image->fresh();
    }

    public function recognize(Request $request)
    {
        $tesseract = new TesseractOCR(storage_path() . '/app/' . Str::after($request->image_path,'/storage'));
        return $tesseract->run();
    }
}
