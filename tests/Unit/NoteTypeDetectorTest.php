<?php

namespace Tests\Unit;

use Tests\TestCase;

class NoteTypeDetectorTest extends TestCase
{
    public function test_note_type_detector_detects_images()
    {
        $empty_note = Note::factory()->create();
        $note_with_images = Note::factory()->has( Image::factory() )->create();
    }
}
