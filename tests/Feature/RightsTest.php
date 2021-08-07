<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RightsTest extends TestCase
{
    public function test_note_could_be_duplicated_by_owner()
    {
        $note = Note::factory()->create();
        $collaborator = User::factory()->create();
        $note->collaborators()->attach($collaborator);
        $note->refresh();

        auth()->login($note->owner); //check for owner's rights
        $this->post( route('note.duplicate', $note) )->assertOk();

        auth()->login($note->collaborators[0]); //check for owner's rights
        $this->post( route('note.duplicate', $note) )->assertForbidden();
    }
}
