<?php

namespace Tests\Unit;

use App\Models\Note;
use App\Models\User;
use Tests\TestCase;

class CollaboratorTest extends TestCase
{
    public function test_a_note_knows_collaborators()
    {
        $owner = User::factory()->create();
        $users = User::factory()->count(3);
        $note = Note::factory()->for($owner, 'owner')
                               ->hasAttached($users, [], 'collaborators')
                               ->create();

        $this->assertInstanceOf(User::class, $note->collaborators->first());
        $this->assertCount(3, $note->collaborators);

        $json = $note->toJson();
        $obj = json_decode($json);

        $this->assertNotEmpty($obj->collaborators_json);
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
