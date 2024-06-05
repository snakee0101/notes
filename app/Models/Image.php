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
    protected $appends = ['image_url', 'thumbnail_small_url', 'thumbnail_large_url'];

    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        static::created(function(self $image) {
            $image->update([ 'recognized_text' => $image->recognize() ]);
        });
    }

    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }

    public function getThumbnailSmallUrlAttribute()
    {
        return asset('storage/' . $this->thumbnail_small_path);
    }

    public function getThumbnailLargeUrlAttribute()
    {
        return asset('storage/' . $this->thumbnail_large_path);
    }

    public static function removeSoftDeleted()
    {
        static::onlyTrashed()->each(function(self $image) {
            Storage::disk('public')->delete([
                $image->image_path, $image->thumbnail_small_path, $image->thumbnail_large_path
            ]);

            $image->forceDelete();
        });
    }

    public function note()
    {
        return $this->belongsTo(Note::class);
    }

    public function recognize()
    {
        $ocr_service = app(TesseractOCR::class);
        $ocr_service->image(Storage::disk('public')->path($this->image_path));

        try {
            $recognized_text = $ocr_service->run();
        } catch(\Exception $e) {
            $recognized_text = null;
        }

        return $recognized_text;
    }

    public function makeCopy(Note $replica)
    {
        $extension = pathinfo(Storage::disk('public')->path($this->image_path), PATHINFO_EXTENSION);
        $new_filename = Str::random(50) . ".$extension";

        Storage::disk('public')->copy($this->image_path, $image_path = "images/$new_filename");
        Storage::disk('public')->copy($this->thumbnail_small_path, $thumbnail_small_path = "thumbnails_small/$new_filename");
        Storage::disk('public')->copy($this->thumbnail_large_path, $thumbnail_large_path = "thumbnails_large/$new_filename");

        $replica->images()->create(
            compact('image_path', 'thumbnail_small_path', 'thumbnail_large_path')
        );
    }

    public static function processUpload(UploadedFile $image)
    {
        $filename = pathinfo(
            $image->store('images', 'public'), PATHINFO_BASENAME
        );

        $image_path = "images/$filename";
        $thumbnail_small_path = "thumbnails_small/$filename";
        $thumbnail_large_path = "thumbnails_large/$filename";

        $intervention_image = InterventionImage::make( Storage::disk('public')->path($image_path) );

        if($intervention_image->width() <= 240) { //if image is smaller than 240 pixels - use image itself as a both thumbnails
            $thumbnail_small_path = $thumbnail_large_path = $image_path;
        } elseif ($intervention_image->width() <= 600) { //if image is smaller than 600 pixels - generate small thumbnail and use it as both thumbnails
            $thumbnail_large_path = $thumbnail_small_path;
            $intervention_image->widen(240)->save(Storage::disk('public')->path($thumbnail_small_path), 100);
        } else { //if image is bigger than 600 pixels - generate small and large thumbnail separately
            $intervention_image->widen(600)->save(Storage::disk('public')->path($thumbnail_large_path), 100);
            $intervention_image->widen(240)->save(Storage::disk('public')->path($thumbnail_small_path), 100);
        }

        return compact('image_path', 'thumbnail_small_path', 'thumbnail_large_path');
    }
}
