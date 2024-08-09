<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image as InterventionImage;

class Drawing extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $appends = ['image_encoded', 'thumbnail_encoded'];
    protected $hidden = ['image', 'thumbnail'];

    public $timestamps = false;
    protected $touches = ['note'];

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
