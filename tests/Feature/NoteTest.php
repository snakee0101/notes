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

    /**
     * @var array
     */
    private $userData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userData = [
            'header' => 'header',
            'body' => 'body',
            'pinned' => false,
            'archived' => false,
            'color' => 'blue',
            'type' => 'text'
        ];
    }

    public function test_a_note_could_be_created()
    {
        $user = User::factory()->make();
        auth()->login($user);

        $response = $this->post(route('note.store'), $this->userData);
        $this->assertNotNull($note = Note::first());

        $this->assertEquals($this->userData['header'], $note->header);
        $this->assertEquals($this->userData['body'], $note->body);
        $this->assertEquals($this->userData['pinned'], $note->pinned);
        $this->assertEquals($this->userData['archived'], $note->archived);
        $this->assertEquals($this->userData['color'], $note->color);
        $this->assertEquals($this->userData['type'], $note->type);
    }
}
