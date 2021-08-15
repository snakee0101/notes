<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\Reminder;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

        $this->assertDatabaseHas('reminders', ['repeat' => '{"every":{"number":2,"unit":"day"},"ends":{"after":3}}']);
        $this->assertEquals(3, Reminder::first()->repeat->ends->after);
        $this->assertEquals(2, Reminder::first()->repeat->every->number);
        $this->assertEquals('day', Reminder::first()->repeat->every->unit);
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

    /*public function test_reminder_could_be_stored_for_specific_place()
    {

    }*/
}
