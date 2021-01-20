<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_example()
    {
        $user = User::factory()->make();
        auth()->login($user);

        $response = $this->post( route('note.store') );
        $this->assertNotNull( Note::first() );
    }
}
