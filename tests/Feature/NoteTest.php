<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\Reminder;
use App\Models\User;
use Database\Factories\UserFactory;
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
        $user = UserFactory::times(1)->createOne();
        auth()->login($user);

        $this->post(route('note.store'), $this->userData);
        $this->assertNotNull($note = Note::first());

        $this->assertEquals($this->userData['header'], $note->header);
        $this->assertEquals($this->userData['body'], $note->body);
        $this->assertEquals($this->userData['pinned'], $note->pinned);
        $this->assertEquals($this->userData['archived'], $note->archived);
        $this->assertEquals($this->userData['color'], $note->color);
        $this->assertEquals($this->userData['type'], $note->type);
    }

    public function test_a_note_could_be_saved_with_reminder()
    {
        $user = UserFactory::times(1)->createOne();
        auth()->login($user);

        $this->userData['reminder_json'] = "{
            \"repeat\": {
                \"every\": {
                     \"number\":3,
                     \"unit\":\"week\",
                     \"weekdays\":[\"Wednesday\",\"Friday\",\"Tuesday\"]
                },
                \"ends\": {
                     \"after\":\"\",
                     \"on_date\":\"2021-06-21 00:00:00\"
                }
            },
            \"time\": \"2021-06-09 17:00:00\"
        }";

        $this->post(route('note.store'), $this->userData );

        $this->assertNotNull($note = Note::first());
        $this->assertNotNull($reminder = Reminder::first());

        $this->assertEquals("2021-06-09 17:00:00", $reminder->time);
        $this->assertJson("{\"repeat\": {
                \"every\": {
                     \"number\":3,
                     \"unit\":\"week\",
                     \"weekdays\":[\"Wednesday\",\"Friday\",\"Tuesday\"]
                },
                \"ends\": {
                     \"after\":\"\",
                     \"on_date\":\"2021-06-21 00:00:00\"
                }
            }}", json_encode($reminder->repeat));
    }

     public function test_a_note_could_be_updated()
     {
        $note = Note::factory()->create();
        auth()->login($note->owner);
        $this->put(route('note.update', $note), $this->changes);

        $note->refresh();

        $this->assertEquals($this->changes['header'], $note->header);
        $this->assertEquals($this->changes['body'], $note->body);
        $this->assertEquals($this->changes['pinned'], $note->pinned);
        $this->assertEquals($this->changes['archived'], $note->archived);
        $this->assertEquals($this->changes['color'], $note->color);
        $this->assertEquals($this->changes['type'], $note->type);
     }

     public function test_a_note_could_be_deleted()
     {
        $note = Note::factory()->for(User::factory(), 'owner')->create();
        auth()->login($note->owner);

        $this->delete( route('note.destroy', $note) );
        $this->assertSoftDeleted($note);

        $this->delete( route('note.destroy',$note) );
        $this->assertEmpty( Note::onlyTrashed()->get() );
     }

    public function test_a_note_could_be_restored()
    {
        $note = Note::factory()->for(User::factory(), 'owner')->create();
        auth()->login($note->owner);

        $note->delete();
        $this->assertEmpty( Note::all() );
        $this->assertNotEmpty( Note::onlyTrashed()->get() );

        $this->post( route('note.restore', $note->id) );

        $this->assertEmpty( Note::onlyTrashed()->get() );
        $this->assertNotEmpty( Note::all() );
    }

    public function test_archived_note_could_be_updated()
    {
        $note = Note::factory()->create(['archived' => true]);
        auth()->login($note->owner);
        $this->put( route('note.destroy', $note), ['body' => 'new content'] );

        $this->assertEquals('new content', $note->fresh()->body);
    }

    public function test_archived_note_could_be_deleted()
    {
        $note = Note::factory()->create(['archived' => true]);
        auth()->login($note->owner);
        $this->delete( route('note.destroy', $note) );

        $this->assertSoftDeleted($note->fresh());
    }

    public function test_archived_note_could_be_restored()
    {
        $note = Note::factory()->create(['deleted_at' => now(), 'archived' => true]);
        auth()->login($note->owner);

        $this->post( route('note.restore', $note) );

        $this->assertNull( $note->fresh()->deleted_at );
    }

    public function test_a_note_could_be_pinned_and_unpinned()
    {
        $note = Note::factory()->create(['pinned' => false]);
        auth()->login($note->owner);

        $this->put( route('note.update', $note), [
            'pinned' => true
        ] );
        $this->assertTrue( $note->fresh()->pinned );

        $this->put( route('note.update', $note), [
            'pinned' => false
        ] );
        $this->assertFalse( $note->fresh()->pinned );
    }

    public function test_a_note_could_be_duplicated()
    {
        $note = Note::factory()->create();
        auth()->login($note->owner);

        $this->assertDatabaseCount('notes', 1);

        $this->post( route('note.duplicate', $note) );

        $this->assertDatabaseCount('notes', 2);
    }
}
