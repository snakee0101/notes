<?php

namespace Tests\Unit;

use \App\Models\{Note, Image};
use App\Utilities\NoteTypeDetector;

use Tests\TestCase;

class NoteTypeDetectorTest extends TestCase
{
    public $empty_note, $note_with_images;

    protected function setUp(): void
    {
        parent::setUp();
        $this->empty_note = Note::factory()->create();
        $this->note_with_images = Note::factory()->has( Image::factory() )->create();
    }

    public function test_note_type_detector_could_be_initialized_statically()
    {
        $this->assertInstanceOf(NoteTypeDetector::class, NoteTypeDetector::select($this->empty_note));
    }

    /*public function test_note_type_detector_detects_images()
    {

    }*/
}
