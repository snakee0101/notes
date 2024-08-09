<?php

namespace Tests\Feature;

use App\Models\Drawing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;
use Tests\TestCase;
use App\Models\Note;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use thiagoalessio\TesseractOCR\TesseractOCR;

class DrawingTest extends TestCase
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

    public function test_note_has_many_drawings()
    {
        Drawing::factory()->create([
            'note_id' => $note = Note::factory()->create()
        ]);

        $this->assertInstanceOf(Drawing::class, $note->drawings()->first());
    }

    public function test_a_drawing_could_be_uploaded_and_it_is_attached_to_the_note()
    {
        $note = Note::factory()->create();
        auth()->login($note->owner);

        $response = $this->post( route('drawing.store'), [
            'image' => $this->generate_image(),
            'note_id' => $note->id
        ])->assertSuccessful();

        $image = $response->json();

        $this->assertEquals($note->id, $image["note_id"]);
        $this->assertInstanceOf(Drawing::class, $note->fresh()->drawings()->first());
        $this->assertEquals($image["image_encoded"], $note->fresh()->drawings()->first()->image_encoded);
    }
}
