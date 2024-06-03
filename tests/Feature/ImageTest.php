<?php

namespace Tests\Feature;

use App\Models\Image;
use App\Models\Note;
use App\Models\User;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Str;
use \Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

        Storage::fake();
        Storage::makeDirectory('images');
        Storage::makeDirectory('thumbnails_large');
        Storage::makeDirectory('thumbnails_small');
    }

    public function generate_image() : File
    {
        return UploadedFile::fake()
               ->image('test.jpg', 1000, 1000);
    }

    public function test_image_belongs_to_a_note()
    {
        $storage = Storage::fake();
        $note = Note::factory()->create();
        auth()->login($note->owner);

        $storage->put('images/123.jpeg', '12345');
        $storage->put('thumbnails_small/456.jpeg', '12345');
        $storage->put('thumbnails_large/789.jpeg', '12345');

        $note->images()->create([
            'note_id' => $note->id,
            'image_path' => '/storage/images/123.jpeg',
            'thumbnail_small_path' => '/storage/thumbnails_small/456.jpeg',
            'thumbnail_large_path' => '/storage/thumbnails_large/789' .
                '.jpeg',
        ]);

        $note->refresh();
        $image = $note->images[0];

        $this->assertInstanceOf(Note::class, $image->note);
    }

    public function test_an_image_could_be_uploaded()
    {
       $note = Note::factory()->create();
       auth()->login($note->owner);

        $response = $this->post( route('image.store'), [
           'image' => $this->generate_image(),
           'note_id' => $note->id
       ])->assertSuccessful();

       $images = $response->json();

       Storage::assertExists(Str::after($images["image_path"], '/storage'));
       Storage::assertExists(Str::after($images["thumbnail_small_path"], '/storage'));
       Storage::assertExists(Str::after($images["thumbnail_large_path"], '/storage'));
    }

    public function test_an_image_is_attached_to_the_note()
    {
        $note = Note::factory()->create();
        auth()->login($note->owner);

        $this->assertCount(0, $note->fresh()->images);

        $images = $this->post( route('image.store'), [
            'image' => $this->generate_image(),
            'note_id' => $note->id
        ])->json();

        $image = $note->fresh()->images()->first();

        $this->assertInstanceOf(Image::class, $image);

        $this->assertEquals($image->image_path, $images['image_path']);
        $this->assertEquals($image->thumbnail_small_path, $images['thumbnail_small_path']);
        $this->assertEquals($image->thumbnail_large_path, $images['thumbnail_large_path']);
    }

    public function test_tesseractOCR_service_itself()
    {
        $image = imagecreate(200, 200);
        $color = imagecolorallocate($image, 255, 255, 255);
        $text_color = imagecolorallocate($image, 0, 0, 0);
        $font_path = 'storage/app/Roboto-Light.ttf';

        imagefttext($image, 20, 0, 40,40, $text_color, $font_path,'test OCR');
        imagejpeg($image, Storage::path('test_OCR.jpg'));

        $tesseract = new TesseractOCR( Storage::path('test_OCR.jpg') );
        $recognized_text = $tesseract->run();

        imagedestroy($image);

        $this->assertEquals('test OCR', $recognized_text);
    }

    public function test_an_image_could_be_soft_deleted_by_id()
    {
        $storage = Storage::fake();
        $note = Note::factory()->create();
        auth()->login($note->owner);

        $storage->put('images/123.jpeg', '12345');
        $storage->put('thumbnails_small/456.jpeg', '12345');
        $storage->put('thumbnails_large/789.jpeg', '12345');

        $note->images()->create([
            'note_id' => $note->id,
            'image_path' => '/storage/images/123.jpeg',
            'thumbnail_small_path' => '/storage/thumbnails_small/456.jpeg',
            'thumbnail_large_path' => '/storage/thumbnails_large/789' .
                '.jpeg',
        ]);

        $note->refresh();

        Storage::assertExists('images/123.jpeg');
        Storage::assertExists('thumbnails_small/456.jpeg');
        Storage::assertExists('thumbnails_large/789.jpeg');

        $this->assertDatabaseCount('images', 1);

        $image_id = $this->post( route('image.destroy', $note->images[0]))->content();

        $this->assertSoftDeleted('images', ['id' => $image_id]);
    }

    public function test_image_deletion_could_be_undone()
    {
        $storage = Storage::fake();
        $note = Note::factory()->create();
        auth()->login($note->owner);

        $storage->put('images/123.jpeg', '12345');
        $storage->put('thumbnails_small/456.jpeg', '12345');
        $storage->put('thumbnails_large/789.jpeg', '12345');

        $note->images()->create([
            'note_id' => $note->id,
            'image_path' => '/storage/images/123.jpeg',
            'thumbnail_small_path' => '/storage/thumbnails_small/456.jpeg',
            'thumbnail_large_path' => '/storage/thumbnails_large/789' .
                '.jpeg',
        ]);
        $image = $note->images()->first();

        $note->images()->first()->delete();
        $note->refresh();
        $image->refresh();

        $this->assertSoftDeleted('images', ['id' => $image->id]);

        $image = json_decode($this->put("/image/restore/$image->id")->assertOk()->content());
        $this->assertNull( Image::where('thumbnail_large_path', $image->thumbnail_large_path)->first()->deleted_at );
    }

    public function test_images_are_physically_deleted_after_note_force_deleting()
    {
        $storage = Storage::fake();
        $note = Note::factory()->create();
        auth()->login($note->owner);

        $storage->put('images/123.jpeg', '12345');
        $storage->put('thumbnails_small/456.jpeg', '12345');
        $storage->put('thumbnails_large/789.jpeg', '12345');

        $note->images()->create([
            'note_id' => $note->id,
            'image_path' => '/storage/images/123.jpeg',
            'thumbnail_small_path' => '/storage/thumbnails_small/456.jpeg',
            'thumbnail_large_path' => '/storage/thumbnails_large/789' .
                '.jpeg',
        ]);

        $note->forceDelete();

        Storage::assertMissing('images/123.jpeg');
        Storage::assertMissing('thumbnails_small/456.jpeg');
        Storage::assertMissing('thumbnails_large/789.jpeg');
    }

    public function test_images_are_physically_deleted_after_five_minutes_if_deletion_is_not_undone()
    {

    }
}
