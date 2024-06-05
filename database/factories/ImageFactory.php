<?php

namespace Database\Factories;

use App\Models\Image;
use App\Models\Note;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;

class ImageFactory extends Factory
{
    use WithFaker;

    protected $model = Image::class;

    public function definition()
    {
        Storage::fake('public');

        Storage::put($image_path = 'images/123.jpeg', 12345);
        Storage::put($thumbnail_small_path = 'thumbnails_small/456.jpeg', 12345);
        Storage::put($thumbnail_large_path = 'thumbnails_large/789.jpeg', 12345);

        return ['note_id' => Note::factory()] + compact('image_path', 'thumbnail_small_path', 'thumbnail_large_path');
    }
}
