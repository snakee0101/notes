<?php

namespace Tests\Feature;

use App\Models\Image;
use App\Models\Note;
use Illuminate\Http\Testing\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;
use Tests\TestCase;

use thiagoalessio\TesseractOCR\TesseractOCR;

class ImageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(TesseractOCR::class, function (MockInterface $mock) {
            $mock->shouldReceive('run', 'image', 'imageData');
        });

        Storage::fake('public');
    }

    public function generate_image() : File
    {
        return UploadedFile::fake()
               ->image('test.jpg', 1000, 1000);
    }

    public function test_image_belongs_to_a_note()
    {
        $image = Image::factory()->create([
            'note_id' => Note::factory()
        ]);

        $this->assertInstanceOf(Note::class, $image->note);
    }

    public function test_an_image_could_be_uploaded_and_it_is_attached_to_the_note()
    {
       $note = Note::factory()->create();
       auth()->login($note->owner);

       $response = $this->post( route('image.store'), [
           'image' => $this->generate_image(),
           'note_id' => $note->id
       ])->assertSuccessful();

       $image = $response->json();

       $this->assertEquals($note->id, $image["note_id"]);
       $this->assertInstanceOf(Image::class, $note->fresh()->images()->first());
       $this->assertEquals($image["image_encoded"], $note->fresh()->images()->first()->image_encoded);
    }

    public function test_an_image_could_be_soft_deleted()
    {
        $image = Image::factory()->create([
            'note_id' => Note::factory()
        ]);

        $this->actingAs($image->note->owner);

        $this->post(route('image.destroy', $image));

        $this->assertSoftDeleted($image->fresh());
    }

    public function test_image_deletion_could_be_undone()
    {
        $image = Image::factory()->create([
            'note_id' => Note::factory()
        ]);

        $this->actingAs($image->note->owner);

        $this->post(route('image.destroy', $image));

        $this->assertSoftDeleted($image->fresh());

        $image = json_decode($this->put("/image/restore/$image->id")->assertOk()->content());
        $this->assertNotSoftDeleted('images', ['id' => $image->id]);
    }
}
