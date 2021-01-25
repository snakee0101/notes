<?php

namespace Database\Factories;

use App\Models\Image;
use App\Models\Note;
use Illuminate\Database\Eloquent\Factories\Factory;

class ImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Image::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'note_id' => Note::factory(),
            'image_path' => '/images/1',
            'thumbnail_path' => '/thumbnails/1',
        ];
    }
}
