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

    private $userData;
    private $changes;

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

        $this->changes = [
            'header' => 'new header',
            'body' => 'new body',
            'pinned' => true,
            'archived' => true,
            'color' => 'red',
            'type' => 'list'
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

     public function test_a_note_could_be_updated() {
        $note = Note::factory()->create();
        $this->put(route('note.update', $note), $this->changes);

        $note->refresh();

        $this->assertEquals($this->changes['header'], $note->header);
        $this->assertEquals($this->changes['body'], $note->body);
        $this->assertEquals($this->changes['pinned'], $note->pinned);
        $this->assertEquals($this->changes['archived'], $note->archived);
        $this->assertEquals($this->changes['color'], $note->color);
        $this->assertEquals($this->changes['type'], $note->type);
     }

     public function test_a_note_could_be_deleted() {
        $note = Note::factory()->for(User::factory(), 'owner')->create();

        $this->delete( route('note.destroy', $note->id) );
        $this->assertSoftDeleted($note);

        $this->delete( route('note.destroy',$note->fresh()->id) );
        $this->assertEmpty( Note::withTrashed()->get() );
     }
}
