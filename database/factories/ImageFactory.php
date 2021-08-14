<?php

namespace Database\Factories;

use App\Models\Image;
use App\Models\Note;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\WithFaker;

class ImageFactory extends Factory
{
    use WithFaker;

    protected $model = Image::class;

    public function definition()
    {
        return [
            'note_id' => Note::factory(),
            'image_path' => '/storage/images/1.jpeg',
            'thumbnail_small_path' => '/storage/thumbnails_small/456.jpeg',
            'thumbnail_large_path' => '/storage/thumbnails_large/789' . '.jpeg',
            'recognized_text' => $this->faker->sentence
        ];
    }
}
