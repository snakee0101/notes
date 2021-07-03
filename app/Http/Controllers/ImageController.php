<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ImageController extends Controller
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
     * @param \Illuminate\Http\Request $request
     * @param Note $note
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $image = $request->file('image');
        $paths = Image::processUpload($image);

        $note = Note::find( $request->input('note_id') );
        $note->images()->create([
            'note_id' => $note->id,
            'image_path' => $paths['image_path'],
            'thumbnail_small_path' => $paths['thumbnail_small_path'],
            'thumbnail_large_path' => $paths['thumbnail_large_path'],
        ]);

        return $paths;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function show(Image $image)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function edit(Image $image)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Image $image)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $image = Image::where('thumbnail_large_path', request('thumbnail_large_path'))
            ->first();

        $image_id = $image->id;
        $image->delete();

        return $image_id;
    }

    public function undoDelete()
    {
      /*  $temp_name = now()->timestamp . random_int(10000,10000000) . '.jpg';
        Storage::put("/tmp/$temp_name", request()->input('image_content'));

        $image = new UploadedFile(Storage::path("/tmp/$temp_name"), 'test.jpg', 'image/*', null, false);

        $paths = Image::processUpload($image);

        $note = Note::find( request()->input('note_id') );
        $note->images()->create([
            'note_id' => $note->id,
            'image_path' => $paths['image_path'],
            'thumbnail_small_path' => $paths['thumbnail_small_path'],
            'thumbnail_large_path' => $paths['thumbnail_large_path'],
        ]);

        return $paths;*/
    }
}
