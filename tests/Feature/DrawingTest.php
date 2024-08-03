<?php

namespace Tests\Feature;

use App\Models\Drawing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Note;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class DrawingTest extends TestCase
{
    use RefreshDatabase;

    public function test_note_has_many_drawings()
    {
        Drawing::factory()->create([
            'note_id' => $note = Note::factory()->create()
        ]);

        $this->assertInstanceOf(Drawing::class, $note->drawings()->first());
    }
}
