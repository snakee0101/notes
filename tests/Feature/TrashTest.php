<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use App\Utilities\Trash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TrashTest extends TestCase
{
    public function test_a_user_can_empty_the_trash()
    {
        $user = User::factory()->create();

        $notes = Note::factory()->for($user, 'owner')->count(5)->create();
        $notes->each->delete();
        auth()->login($user);

        $this->assertCount(5, Note::onlyTrashed()->get());

        $this->delete(route('trash.empty'));
        $this->assertEmpty( Note::onlyTrashed()->get() );
    }
}
