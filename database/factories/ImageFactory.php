<?php

namespace Database\Factories;

use App\Models\Image;
use App\Models\Note;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;

class ImageFactory extends Factory
{
    use WithFaker;

    protected $model = Image::class;

    public function definition()
    {
        $uploaded_image = UploadedFile::fake()->image('image.jpg');
        $encoded_image = utf8_encode($uploaded_image->getContent());

        return [
            'note_id' => Note::factory(),
            'image' => $encoded_image,
            'thumbnail' => $encoded_image
        ];
    }

    public function forOCR()
    {
        $image = imagecreate(200, 200);
        $color = imagecolorallocate($image, 255, 255, 255);
        $text_color = imagecolorallocate($image, 0, 0, 0);
        $font_path = 'storage/app/Roboto-Light.ttf';

        imagefttext($image, 20, 0, 40,40, $text_color, $font_path,'test OCR');

        ob_start();
        imagejpeg($image);
        $imageData = ob_get_contents();
        ob_end_clean();

        $encoded_image = utf8_encode($imageData);

        imagedestroy($image);

        return $this->state(function (array $attributes) use ($encoded_image) {
            return [
                'image' => $encoded_image,
                'thumbnail' => $encoded_image
            ];
        });
    }
}
