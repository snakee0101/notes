<?php

namespace Tests\Feature;

use App\Models\Image;
use App\Models\Note;
use Illuminate\Http\Testing\File;
use \Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageTest extends TestCase
{
    public function generate_image() : File
    {
        Storage::fake();
        return UploadedFile::fake()
               ->image('test.jpg', 1000, 1000);
    }

    public function test_an_image_could_be_uploaded()
    {
       $note = Note::factory()->create();
       auth()->login($note->owner);

       $image = $this->generate_image();

       $response = $this->post( route('image.store'), [
           'image' => $image,
           'note_id' => $note->id
       ]);
       $filename = $response->content();

       $this->assertTrue(
            Storage::exists('/images/' . $filename)
       );
    }

    public function test_an_image_is_attached_to_the_note()
    {
        $note = Note::factory()->create();
        auth()->login($note->owner);

        $image = $this->generate_image();

        $this->post( route('image.store'), [
            'image' => $image,
            'note_id' => $note->id
        ]);

        $this->assertCount(1, $note->fresh()->images);
        $this->assertInstanceOf(Image::class, $note->fresh()->images()->first());
    }

    public function test_thumbnail_small_is_attached()
    {

    }

    public function test_thumbnail_large_is_attached()
    {

    }
}
