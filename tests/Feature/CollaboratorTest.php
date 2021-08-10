<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CollaboratorTest extends TestCase
{
    public function test_collaborator_can_be_synchronized()
    {
        $owner = User::factory()->create();
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $note = Note::factory()->for($owner, 'owner')->create();

        auth()->login($owner);

        $this->assertEmpty($note->collaborators);

        $this->post(route('sync_collaborator', [
            'note' => $note,
        ]), [
            'collaborator_ids' => [$user->id, $user2->id]
        ]);

        $this->assertContains($user->id, $note->fresh()->collaborators->pluck('id'));
        $this->assertContains($user2->id, $note->fresh()->collaborators->pluck('id'));

        $this->post(route('sync_collaborator', [
            'note' => $note,
        ]), [
            'collaborator_ids' => [$user->id]
        ]);

        $this->assertContains($user->id, $note->fresh()->collaborators->pluck('id'));
        $this->assertNotContains($user2->id, $note->fresh()->collaborators->pluck('id'));
    }

    public function test_check_user_existence()
    {
        $user = User::factory()->create();
        auth()->login($user);

        $response = $this->get( route('check_user_existence', $user->email) );
        $response->assertJson(['exists' => true]);

        $response = $this->get( route('check_user_existence', 'not-exists@gmail.com') );
        $response->assertJson(['exists' => false]);
    }

    public function test_note_can_restore_collaborator_emails()
    {
        $owner = User::factory()->create();
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $note = Note::factory()->for($owner, 'owner')->create();

        auth()->login($owner);

        $this->post(route('sync_collaborator', $note), [
            'emails' => [$user->email, $user2->email]
        ])->assertJson([$user->email, $user2->email]);
    }
}
