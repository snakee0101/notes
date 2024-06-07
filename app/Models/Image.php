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

    protected $appends = ['image_encoded', 'thumbnail_small_encoded', 'thumbnail_large_encoded'];
    protected $hidden = ['image', 'thumbnail_small', 'thumbnail_large'];

    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        static::created(function(self $image) {
            $image->update([ 'recognized_text' => $image->recognize() ]);
        });
    }

    public function getImageEncodedAttribute()
    {
        return base64_encode(utf8_decode($this->image));
    }

    public function getThumbnailSmallEncodedAttribute()
    {
        return base64_encode(utf8_decode($this->thumbnail_small));
    }

    public function getThumbnailLargeEncodedAttribute()
    {
        return base64_encode(utf8_decode($this->thumbnail_large));
    }

    /*public function getImageUrlAttribute()
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
    }*/

    public static function removeSoftDeleted()
    {
        static::onlyTrashed()->each(function(self $image) {
            /*Storage::disk('public')->delete([
                $image->image_path, $image->thumbnail_small_path, $image->thumbnail_large_path
            ]);*/

            $image->forceDelete();
        });
    }

    public function note()
    {
        return $this->belongsTo(Note::class);
    }

    public function recognize()
    {
        /*$ocr_service = app(TesseractOCR::class);
        $ocr_service->image(Storage::disk('public')->path($this->image_path));

        try {
            $recognized_text = $ocr_service->run();
        } catch(\Exception $e) {
            $recognized_text = null;
        }*/

        return $recognized_text = "temporary recognized text";
    }

    public function makeCopy(Note $replica)
    {
        /*$extension = pathinfo(Storage::disk('public')->path($this->image_path), PATHINFO_EXTENSION);
        $new_filename = Str::random(50) . ".$extension";

        Storage::disk('public')->copy($this->image_path, $image_path = "images/$new_filename");
        Storage::disk('public')->copy($this->thumbnail_small_path, $thumbnail_small_path = "thumbnails_small/$new_filename");
        Storage::disk('public')->copy($this->thumbnail_large_path, $thumbnail_large_path = "thumbnails_large/$new_filename");

        $replica->images()->create(
            compact('image_path', 'thumbnail_small_path', 'thumbnail_large_path')
        );*/
    }

    public static function processUpload(UploadedFile $uploaded_image)
    {
        $binary_image_data = $uploaded_image->getContent();
        $intervention_image = InterventionImage::make( $binary_image_data );
        $image = utf8_encode($binary_image_data);

        if($intervention_image->width() <= 240) { //if image is smaller than 240 pixels - use image itself as a both thumbnails
            $thumbnail_small = $thumbnail_large = $image;
        } elseif ($intervention_image->width() <= 600) { //if image is smaller than 600 pixels - generate small thumbnail and use it as both thumbnails
            $thumbnail_small = $thumbnail_large = utf8_encode((string)$intervention_image->widen(240)->encode('jpg', 100));
        } else { //if image is bigger than 600 pixels - generate small and large thumbnail separately
            $thumbnail_small = utf8_encode((string)$intervention_image->widen(240)->encode('jpg', 100));
            $thumbnail_large = utf8_encode((string)$intervention_image->widen(600)->encode('jpg', 100));
        }

        return compact('image', 'thumbnail_small', 'thumbnail_large');
    }
}
