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
            'thumbnail_small' => $encoded_image,
            'thumbnail_large' => $encoded_image
        ];
    }
}
