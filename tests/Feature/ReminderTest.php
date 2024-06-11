<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\Reminder;
use App\Models\User;
use App\Notifications\CollaboratorWasAddedNotification;
use App\Notifications\CollaboratorWasDeletedNotification;
use App\Notifications\TimeNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ReminderTest extends TestCase
{
    public function test_reminder_could_be_deleted()
    {
        $note = Note::factory()->create();
        $reminder = Reminder::factory()->create([
            'note_id' => $note->id,
            'user_id' => $note->owner
        ]);
        auth()->login($note->owner);

        $note->refresh();

        $this->assertDatabaseCount('reminders', 1);
        $this->delete( route('reminder.destroy', $note) );
        $this->assertDatabaseCount('reminders', 0);
    }

    public function test_reminder_could_be_stored_for_specific_date()
    {
        $note = Note::factory()->create();
        $this->assertDatabaseCount('reminders', 0);
        auth()->login($note->owner);

        $date = now()->addDay()->format('Y-m-d H:i:s');

        $this->post( route('reminder.store', $note), [
            'time' => $date
        ]);
        $this->assertDatabaseHas('reminders', ['time' => $date]);
    }

    public function test_remainder_could_be_rewritten()
    {
        $note = Note::factory()->create();
        $this->assertDatabaseCount('reminders', 0);
        auth()->login($note->owner);

        $date = now()->addDay()->format('Y-m-d H:i:s');

        $this->post( route('reminder.store', $note), [
            'time' => $date
        ]);

        $this->assertDatabaseHas('reminders', ['time' => $date]);

        $rewritten = now()->addWeek()->format('Y-m-d H:i:s');
        $this->post( route('reminder.store', $note), [
            'time' => $rewritten
        ]);

        $this->assertDatabaseHas('reminders', ['time' => $rewritten]);
    }

    public function test_reminder_could_have_repeat_status()
    {
        $note = Note::factory()->create();
        auth()->login($note->owner);

        $obj = new class() {};
        $obj->every = new class() {
            public $number = 2;
            public $unit = 'day';
        };
        $obj->ends = new class() {
            public $after = 3;
        };

        $this->post( route('reminder.store', $note), [
            'time' => now()->addDay()->format('Y-m-d H:i:s'),
            'repeat' => json_encode($obj)
        ]);

        $first_reminder = Reminder::first();

        $this->assertEquals(3, $first_reminder->repeat->ends->after);
        $this->assertEquals(2, $first_reminder->repeat->every->number);
        $this->assertEquals('day', $first_reminder->repeat->every->unit);
    }

    public function test_a_repeat_status_on_specific_days_of_week_could_be_saved()
    {
        $note = Note::factory()->create();
        auth()->login($note->owner);

        $obj = new class() {};
        $obj->every = new class() {
            public $number = 2;
            public $unit = 'week';
            public $weekdays = ['Monday', 'Tuesday', 'Friday'];
        };
        $obj->ends = new class() {
            public $after = 3;
        };

        $this->post( route('reminder.store', $note), [
            'time' => now()->addDay()->format('Y-m-d H:i:s'),
            'repeat' => json_encode($obj)
        ]);

        $this->assertDatabaseHas('reminders', ['repeat' => '{"every":{"number":2,"unit":"week","weekdays":["Monday","Tuesday","Friday"]},"ends":{"after":3}}']);
        $this->assertIsArray(Reminder::first()->repeat->every->weekdays);
        $this->assertContains('Monday', Reminder::first()->repeat->every->weekdays);
        $this->assertContains('Tuesday', Reminder::first()->repeat->every->weekdays);
        $this->assertContains('Friday', Reminder::first()->repeat->every->weekdays);
    }

    public function test_reminder_could_be_converted_to_json()
    {
        $note = Note::factory()->create();
        $this->assertDatabaseCount('reminders', 0);
        auth()->login($note->owner);

        $date = now()->addDay();

        $reminder = $this->post( route('reminder.store', $note), [
            'time' => $date->format('Y-m-d H:i:s')
        ])->json();

        $this->assertEquals($note->id, $reminder['note_id']);
    }

    public function test_when_collaborators_are_added_appropriate_messages_are_sent()
    {
        Notification::fake();

        $owner = User::factory()->create();
        auth()->login($owner);

        $userData = [
            'header' => 'header',
            'body' => 'body',
            'pinned' => false,
            'archived' => false,
            'color' => 'blue',
            'type' => 'text'
        ];

        $collaborators = User::factory()->times(2)->create();
        $userData['collaborator_ids'] = $collaborators->pluck('id')->toArray();

        $this->post(route('note.store'), $userData);
        $this->assertNotNull($note = Note::first());

        Notification::assertSentTo($collaborators[0], CollaboratorWasAddedNotification::class);
        Notification::assertSentTo($collaborators[1], CollaboratorWasAddedNotification::class);
    }

    public function test_when_collaborators_are_synchronized_appropriate_messages_are_sent()
    {
        Notification::fake();
        Mail::fake();

        $note = Note::factory()->create();
        $users = User::factory()->times(4)->create();
        $note->collaborators()->attach([$users[0]->id, $users[1]->id]);
        $note->refresh();

        auth()->login($note->owner);

        $this->post( route('sync_collaborator', $note), [
            'collaborator_ids' => [$users[0]->id, $users[2]->id,  $users[3]->id]
        ] )->assertOk();


        Notification::assertSentTo($users[1], CollaboratorWasDeletedNotification::class);
        Notification::assertSentTo($users[2], CollaboratorWasAddedNotification::class);
        Notification::assertSentTo($users[3], CollaboratorWasAddedNotification::class);

        Notification::assertNotSentTo($users[0], CollaboratorWasDeletedNotification::class);
        Notification::assertNotSentTo($users[0], CollaboratorWasAddedNotification::class);
    }

    /*public function test_reminder_could_be_stored_for_specific_place()
    {

    }*/
}
