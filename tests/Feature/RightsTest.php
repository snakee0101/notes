<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RightsTest extends TestCase
{
    public function test_note_could_be_duplicated_by_owner_only()
    {
        $note = Note::factory()->create();
        $collaborator = User::factory()->create();
        $note->collaborators()->attach($collaborator);
        $note->refresh();

        auth()->login($note->owner); //check for owner's rights
        $this->post( route('note.duplicate', $note) )->assertOk();

        auth()->login($collaborator); //check for collaborator's rights
        $this->post( route('note.duplicate', $note) )->assertForbidden();
    }

    public function test_note_could_be_removed_and_removed_forever_by_owner_only()
    {
        //create the note to test an owner
        $note = Note::factory()->create();

        auth()->login($note->owner);
        $this->delete( route('note.destroy', $note) )->assertOk();
        $this->delete( route('note.destroy', $note) )->assertOk();

        //create new note to test the collaborator
        $note2 = Note::factory()->create();
        $collaborator = User::factory()->create();
        $note2->collaborators()->attach($collaborator);
        $note2->refresh();

        auth()->login($collaborator);
        $this->delete( route('note.destroy', $note2) )->assertForbidden();

        auth()->login($note2->owner);
        $this->delete( route('note.destroy', $note2) )->assertOk();
        $this->delete( route('note.destroy', $note2) )->assertOk();
    }

    public function test_note_could_be_restored_by_owner_only()
    {
        //create the note to test an owner
        $note = Note::factory()->create();
        $collaborator = User::factory()->create();
        $note->collaborators()->attach($collaborator);
        $note->delete();

        auth()->login($note->owner);
        $this->post( route('note.restore', $note) )->assertOk();

        auth()->login($collaborator);
        $this->post( route('note.restore', $note) )->assertForbidden();
    }

    public function test_note_could_be_returned_to_owner_and_collaborator()
    {
        $note = Note::factory()->create();
        $collaborator = User::factory()->create();
        $note->collaborators()->attach($collaborator);
        $note->refresh();

        auth()->login($note->owner);
        $this->get( route('note.show', $note) )->assertOk();

        auth()->login($collaborator);
        $this->get( route('note.show', $note) )->assertOk();

        auth()->login( User::factory()->create() );
        $this->get( route('note.show', $note) )->assertForbidden();
    }

    public function test_notes_archived_state_could_be_updated_by_owner_only()
    {
        $note = Note::factory()->create( ['archived' => false] );
        $collaborator = User::factory()->create();
        $note->collaborators()->attach($collaborator);
        $note->refresh();

        auth()->login($collaborator);
        $this->put( route('note.update', $note), ['archived' => true] )->assertForbidden();

        auth()->login( User::factory()->create() );
        $this->put( route('note.update', $note), ['archived' => true] )->assertForbidden();

        auth()->login($note->owner);
        $this->put( route('note.update', $note), ['archived' => true] )->assertOk();
    }

    public function test_other_note_properties_could_be_updated_by_owner_and_collaborators()
    {
        $note = Note::factory()->create();
        $collaborator = User::factory()->create();
        $note->collaborators()->attach($collaborator);
        $note->refresh();

        auth()->login($collaborator);
        $this->put( route('note.update', $note), ['header' => 'new header'] )->assertOK();

        auth()->login( User::factory()->create() );
        $this->put( route('note.update', $note), ['header' => 'new header 2'] )->assertForbidden();

        auth()->login($note->owner);
        $this->put( route('note.update', $note), ['header' => 'new header 3'] )->assertOk();
    }
}