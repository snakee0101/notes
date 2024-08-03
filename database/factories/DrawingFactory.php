<?php

namespace Database\Factories;

use App\Models\Drawing;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use App\Models\Note;

class DrawingFactory extends Factory
{
    use WithFaker;

    protected $model = Drawing::class;

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
}
