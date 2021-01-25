<?php

namespace Tests\Feature;

use App\Models\Note;
use \Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageTest extends TestCase
{
    public function test_an_image_could_be_uploaded()
    {
       Storage::fake();

       $note = Note::factory()->create();
       auth()->login($note->owner);

       $image = UploadedFile::fake()
                            ->image('test.jpg', 1000, 1000);

       $response = $this->post( route('image.store'), [
           'image' => $image
       ]);
       $filename = $response->content();

       $this->assertTrue(
            Storage::exists('/images/' . $filename)
       );
    }
}
