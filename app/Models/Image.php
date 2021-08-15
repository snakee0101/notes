<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image as InterventionImage;
use thiagoalessio\TesseractOCR\TesseractOCR;

class Image extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        static::created(function(self $image) {
            $image->update([ 'recognized_text' => $image->recognize() ]);
        });
    }

    public static function removeSoftDeleted()
    {
        //remove images, that were deleted more than a minute ago
        static::onlyTrashed()->where('deleted_at', '<', now()->subMinute())->each(function(self $image) {
            //offset for '/storage/' part
            Storage::delete( substr($image->image_path, 9) );
            Storage::delete( substr($image->thumbnail_small_path, 9) );
            Storage::delete( substr($image->thumbnail_large_path, 9) );

            $image->forceDelete();
        });
    }

    public function note()
    {
        return $this->belongsTo(Note::class);
    }

    public function recognize()
    {
        $path = storage_path() . '/app/' . Str::after($this->image_path, '/storage');
        $tesseract = new TesseractOCR($path);
        return $tesseract->run();
    }

    public function makeCopy(Note $replica)
    {
        $path_1 = substr($this->image_path, 9);  //offset for '/storage/' part
        $path_2 = substr($this->thumbnail_small_path, 9);
        $path_3 = substr($this->thumbnail_large_path, 9);

        $extension = pathinfo($this->image_path)['extension'];
        $new_filename = now()->timestamp . random_int(10000, 10000000) . '.' . $extension;

        Storage::copy($path_1, 'images/' . $new_filename);
        Storage::copy($path_2, 'thumbnails_small/' . $new_filename);
        Storage::copy($path_3, 'thumbnails_large/' . $new_filename);

        $replica->images()->create([
            'note_id' => $replica->id,
            'image_path' => '/storage/images/' . $new_filename,
            'thumbnail_small_path' => '/storage/thumbnails_small/' . $new_filename,
            'thumbnail_large_path' => '/storage/thumbnails_large/' . $new_filename,
        ]);
    }

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

        return compact('image_path', 'thumbnail_small_path', 'thumbnail_large_path');
    }
}
