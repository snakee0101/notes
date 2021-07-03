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
        /*Storage::fake();
        return UploadedFile::fake()
               ->image('test.jpg', 1000, 1000);*/
    }

    public function test_an_image_could_be_uploaded()
    {
       /*$note = Note::factory()->create();
       auth()->login($note->owner);

       $image = $this->generate_image();

       $response = $this->post( route('image.store'), [
           'image' => $image,
           'note_id' => $note->id
       ]);
       $filename = $response->content();

       dd($response->content());

       $this->assertTrue(
            Storage::exists('/images/' . $filename)
       );*/
    }

    public function test_an_image_is_attached_to_the_note()
    {
        /*$note = Note::factory()->create();
        auth()->login($note->owner);

        $image = $this->generate_image();

        $this->post( route('image.store'), [
            'image' => $image,
            'note_id' => $note->id
        ]);

        $this->assertCount(1, $note->fresh()->images);
        $this->assertInstanceOf(Image::class, $note->fresh()->images()->first());*/
    }

    public function test_thumbnail_small_is_attached()
    {

    }

    public function test_thumbnail_large_is_attached()
    {

    }

    public function test_an_image_could_be_deleted_by_thumbnail_large_path()
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

        $this->assertTrue( $storage->exists('images/123.jpeg') );
        $this->assertTrue( $storage->exists('thumbnails_small/456.jpeg') );
        $this->assertTrue( $storage->exists('thumbnails_large/789.jpeg') );
        $this->assertDatabaseCount('images', 1);

        $image_content = $this->post( route('image.destroy'), [
            'thumbnail_large_path' => $note->images[0]->thumbnail_large_path
        ] )->content();

        $this->assertEquals('12345', $image_content);

        $this->assertFalse( $storage->exists('images/123.jpeg') );
        $this->assertFalse( $storage->exists('thumbnails_small/456.jpeg') );
        $this->assertFalse( $storage->exists('thumbnails_large/789.jpeg') );
        $this->assertDatabaseCount('images', 0);
    }

    public function test_images_are_physically_deleted_when_deleting_the_note()
    {
        $storage = Storage::fake();
        $note = Note::factory()->create();
        $storage->put('images/123.jpeg', 12345);
        $storage->put('thumbnails_small/456.jpeg', 12345);
        $storage->put('thumbnails_large/789.jpeg', 12345);

        $note->images()->create([
            'note_id' => $note->id,
            'image_path' => '/storage/images/123.jpeg',
            'thumbnail_small_path' => '/storage/thumbnails_small/456.jpeg',
            'thumbnail_large_path' => '/storage/thumbnails_large/789' .
                '.jpeg',
        ]);

        $this->assertTrue( $storage->exists('images/123.jpeg') );
        $this->assertTrue( $storage->exists('thumbnails_small/456.jpeg') );
        $this->assertTrue( $storage->exists('thumbnails_large/789.jpeg') );
        $this->assertDatabaseCount('images', 1);

        $note->forceDelete();

        $this->assertFalse( $storage->exists('images/123.jpeg') );
        $this->assertFalse( $storage->exists('thumbnails_small/456.jpeg') );
        $this->assertFalse( $storage->exists('thumbnails_large/789.jpeg') );
        $this->assertDatabaseCount('images', 0);

    }
}
