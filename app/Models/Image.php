<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image as InterventionImage;
use thiagoalessio\TesseractOCR\TesseractOCR;

class Image extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $appends = ['image_encoded', 'thumbnail_encoded'];
    protected $hidden = ['image', 'thumbnail'];

    public $timestamps = false;
    protected $touches = ['note'];

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

    public function getThumbnailEncodedAttribute()
    {
        return base64_encode(utf8_decode($this->thumbnail));
    }

    public function note()
    {
        return $this->belongsTo(Note::class);
    }

    public function recognize()
    {
        $ocr_service = app(TesseractOCR::class);
        $ocr_service->imageData(utf8_decode($this->image),
            InterventionImage::make( utf8_decode($this->image) )->filesize());

        try {
            $recognized_text = $ocr_service->run();
        } catch(\Exception $e) {
            $recognized_text = null;
        }

        return $recognized_text;
    }

    public static function processUpload(UploadedFile $uploaded_image)
    {
        $binary_image_data = $uploaded_image->getContent();
        $intervention_image = InterventionImage::make( $binary_image_data );
        $image = utf8_encode($binary_image_data);

        $thumbnail = $intervention_image->width() <= 240 ? $image
                                                         : utf8_encode((string)$intervention_image->widen(240)->encode('jpg', 100));

        return compact('image', 'thumbnail');
    }
}
