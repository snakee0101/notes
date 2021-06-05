<?php

namespace Tests\Feature;

use App\Models\Note;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NoteToolbarTest extends TestCase
{
    public function test_a_note_could_be_archived()
    {
        $note = Note::factory()->create(['archived' => false]);
        auth()->login($note->owner);
        $this->put( route('note.update', $note), ['archived' => true] );

        $note->refresh();
        $this->assertTrue($note->archived);
    }

    public function test_a_note_could_be_unarchived()
    {
        $note = Note::factory()->create(['archived' => true]);
        auth()->login($note->owner);
        $this->put( route('note.update', $note), ['archived' => false] );

        $note->refresh();
        $this->assertFalse($note->archived);
    }
}
