<?php

namespace Tests\Unit;

use App\Models\Note;
use App\Models\User;
use Tests\TestCase;

class CollaboratorTest extends TestCase
{
    public function test_collaborator_notes_could_be_retrieved()
    {
        $owner = User::factory()->create();
        $user = User::factory()->create();
        $note = Note::factory()->for($user, 'owner') //collaborator has notes
            ->hasAttached($owner, [], 'collaborators')
            ->create();

        $user2 = User::factory()->create();
        $note = Note::factory()->for($user, 'owner')
            ->hasAttached($user2, [], 'collaborators')
            ->create();

        auth()->login($owner);

        $this->assertCount(1, auth()->user()->collaboratorNotes()->get() );
        $this->assertInstanceOf(Note::class, auth()->user()->collaboratorNotes()->first() );
        $this->assertEquals($user->id, auth()->user()->collaboratorNotes()->first()->owner->id );

        //auth()->user()->notes()->where('pinned', true)->paginate();
    }

    public function test_a_note_knows_collaborators()
    {
        $owner = User::factory()->create();
        $users = User::factory()->count(3);
        $note = Note::factory()->for($owner, 'owner')
                               ->hasAttached($users, [], 'collaborators')
                               ->create();

        $note->refresh();
        $this->assertNotEmpty($note->collaborators);
        $this->assertInstanceOf(User::class, $note->collaborators[0]);
    }

    public function test_collaborators_must_be_unique()
    {
        $this->expectExceptionMessage('Integrity constraint violation: 19 UNIQUE constraint failed');

        $owner = User::factory()->create();
        $user = User::factory()->create();
        $note = Note::factory()->for($owner, 'owner')
            ->hasAttached($user, [], 'collaborators')
            ->create();

        $note->collaborators()->attach($user);
    }
}
