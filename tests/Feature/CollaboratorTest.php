<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CollaboratorTest extends TestCase
{
    public function test_collaborator_can_be_attached()
    {
        $owner = User::factory()->create();
        $user = User::factory()->create();
        $note = Note::factory()->for($owner, 'owner')->create();

        auth()->login($owner);

        $this->assertEmpty($note->collaborators);

        $this->post(route('store_collaborator', [
            'note' => $note,
            'user' => $user
        ]));

        $this->assertNotEmpty($note->fresh()->collaborators);
    }
}
