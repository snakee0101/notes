<?php

namespace Tests\Unit;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteTest extends TestCase
{
    public function test_a_note_has_an_owner()
    {
        $note = Note::factory()->for(
            User::factory()->create(), 'owner'
        )->create();

        $this->assertInstanceOf(User::class, $note->owner);
    }

    public function test_archived_notes_are_hidden()
    {
        Note::factory()->for(
            User::factory()->create(), 'owner'
        )->archived()->create();

        $this->assertEmpty( Note::all() );

        Note::factory()->for(
            User::factory()->create(), 'owner'
        )->create();

        $this->assertCount(1, Note::all() );
    }

    public function test_archived_notes_could_be_shown()
    {
        Note::factory()->for(
            User::factory()->create(), 'owner'
        )->archived()->create();

        Note::factory()->for(
            User::factory()->create(), 'owner'
        )->create();

        $this->assertCount(2, Note::withArchived()->get() );
    }

    public function test_only_archived_notes_could_be_shown()
    {
        Note::factory()->for(
            User::factory()->create(), 'owner'
        )->archived()->create();

        Note::factory()->for(
            User::factory()->create(), 'owner'
        )->create();

        $this->assertCount(1, Note::onlyArchived()->get() );
    }
}
