<?php

namespace Database\Factories;

use App\Models\Image;
use App\Models\Note;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageFactory extends Factory
{
    use WithFaker;

    protected $model = Image::class;

    public function definition()
    {
        Storage::fake('public');

        Storage::put($image_path = 'images/'. Str::random(30) .'.jpeg', 12345);
        Storage::put($thumbnail_small_path = 'thumbnails_small/' . Str::random(30) . '.jpeg', 12345);
        Storage::put($thumbnail_large_path = 'thumbnails_large/' . Str::random(30) . '.jpeg', 12345);

        return ['note_id' => Note::factory()] + compact('image_path', 'thumbnail_small_path', 'thumbnail_large_path');
    }
}
