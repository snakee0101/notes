<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as InterventionImage;

class Image extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public static function processUpload(UploadedFile $image)
    {
        $filename = now()->timestamp . random_int(10000,10000000) . '.' . $image->getClientOriginalExtension();
        $image->storeAs('images', $filename);

        $image_path = Storage::url("images/" . $filename);
        $resource = InterventionImage::make( Storage::path("images/" . $filename) );
        $image_width = $resource->width();
        $thumbnail_small_path = '';
        $thumbnail_large_path = '';

        if($image_width <= 240)
        {
            $thumbnail_small_path = Storage::url("images/" . $filename);
            $thumbnail_large_path = Storage::url("images/" . $filename);
        } elseif (($image_width > 240) && ($image_width <= 600)) {
            $thumbnail_small_path = Storage::url('thumbnails_small/' . $filename);
            $thumbnail_large_path = Storage::url('thumbnails_small/' . $filename);
            $resource->widen(240)->save(Storage::path('thumbnails_small/' . $filename), 100);
        } else {
            $thumbnail_small_path = Storage::url('thumbnails_small/' . $filename);
            $thumbnail_large_path = Storage::url('thumbnails_large/' . $filename);
            $resource->widen(600)->save(Storage::path('thumbnails_large/' . $filename), 100);
            $resource->widen(240)->save(Storage::path('thumbnails_small/' . $filename), 100);
        }

        return [
            'image_path' => $image_path,
            'thumbnail_small_path' => $thumbnail_small_path,
            'thumbnail_large_path' => $thumbnail_large_path
        ];
    }
}
