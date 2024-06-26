<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CollaboratorTest extends TestCase
{
    public function test_check_user_existence()
    {
        $user = User::factory()->create();
        auth()->login($user);

        $response = $this->get( route('check_user_existence', $user->email) );
        $response->assertJson(['exists' => true]);
        $response->assertJson(['user' => ['name' => $user->name]]);

        $response = $this->get( route('check_user_existence', 'not-exists@gmail.com') );
        $response->assertJson(['exists' => false]);
        $response->assertJsonMissing(['user' => ['name' => $user->name]]);
    }

    public function test_note_owner_can_update_collaborators_list()
    {
        $owner = User::factory()->create();
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $note = Note::factory()->for($owner, 'owner')->create();

        auth()->login($owner);
        $this->assertEmpty($note->collaborators);

        $this->post(route('sync_collaborator', $note), [
            'emails' => [$user->email, $user2->email]
        ])->assertJson([$user->email, $user2->email]);

        $this->assertContains($user->id, $note->fresh()->collaborators->pluck('id'));
        $this->assertContains($user2->id, $note->fresh()->collaborators->pluck('id'));

        $this->post(route('sync_collaborator', $note), [
            'emails' => [$user->email]
        ])->assertJson([$user->email]);

        $this->assertContains($user->id, $note->fresh()->collaborators->pluck('id'));
        $this->assertNotContains($user2->id, $note->fresh()->collaborators->pluck('id'));
    }
}
