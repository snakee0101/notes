<?php

namespace Tests\Feature;

use App\Models\Image;
use App\Models\Note;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use \Illuminate\Http\UploadedFile;
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
            $mock->shouldReceive('run', 'image');
        });

        Storage::fake('public');
        Storage::disk('public')->makeDirectory('images');
        Storage::disk('public')->makeDirectory('thumbnails_large');
        Storage::disk('public')->makeDirectory('thumbnails_small');
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

       Storage::disk('public')->assertExists($image["image_path"]);
       Storage::disk('public')->assertExists($image["thumbnail_small_path"]);
       Storage::disk('public')->assertExists($image["thumbnail_large_path"]);

       $this->assertEquals($note->id, $image["note_id"]);
       $this->assertInstanceOf(Image::class, $note->fresh()->images()->first());
       $this->assertEquals($image["image_path"], $note->fresh()->images()->first()->image_path);
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

    public function test_images_are_physically_deleted_after_note_force_deleting()
    {
        $image = Image::factory()->create([
            'note_id' => Note::factory()
        ]);

        $image->note->forceDelete();

        Storage::disk('public')->assertMissing($image->image_path);
        Storage::disk('public')->assertMissing($image->thumbnail_small_path);
        Storage::disk('public')->assertMissing($image->thumbnail_large_path);
    }
}
