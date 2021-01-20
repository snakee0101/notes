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
}
