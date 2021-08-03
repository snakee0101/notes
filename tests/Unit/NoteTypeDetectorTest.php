<?php

namespace Tests\Unit;

use App\Models\{Checklist, Note, Image, Task, Link};
use App\Utilities\NoteTypeDetector;

use Tests\TestCase;

class NoteTypeDetectorTest extends TestCase
{
    public Note $empty_note, $note_with_images, $note_with_checklist, $note_with_links;

    protected function setUp(): void
    {
        parent::setUp();
        $this->empty_note = Note::factory()->create();
        $this->note_with_images = Note::factory()->has( Image::factory() )->create();
        $this->note_with_checklist = Note::factory()->has( Checklist::factory()->has(Task::factory()->count(3)) )->create();
        $this->note_with_links = Note::factory()->has( Link::factory() )->create();
    }

    public function test_note_type_detector_could_be_initialized_statically()
    {
        $this->assertInstanceOf(NoteTypeDetector::class, NoteTypeDetector::select($this->empty_note));
    }

    public function test_note_type_detector_detects_images()
    {
        $result = NoteTypeDetector::select($this->note_with_images)->detectTypes();

        $this->assertIsArray($result);
        $this->assertContains('image', $result);
    }

    public function test_note_type_detector_detects_checklists()
    {
        $result = NoteTypeDetector::select($this->note_with_checklist)->detectTypes();

        $this->assertIsArray($result);
        $this->assertContains('checklist', $result);
    }

    public function test_note_type_detector_can_detect_links()
    {
        $result = NoteTypeDetector::select($this->note_with_links)->detectTypes();

        $this->assertIsArray($result);
        $this->assertContains('links', $result);
    }

    public function test_note_type_detector_can_detect_drawings()
    {
        $this->assertFalse('drawings feature is not implemented yet');
    }

    public function test_if_the_note_contains_only_text_note_type_detector_returns_empty_array()
    {
        $result = NoteTypeDetector::select($this->empty_note)->detectTypes();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
}
