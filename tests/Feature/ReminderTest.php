<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\Reminder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReminderTest extends TestCase
{
    public function test_reminder_could_be_deleted()
    {
        $note = Note::factory()->create();
        $reminder = Reminder::factory()->create([
            'note_id' => $note->id
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

        $date = now()->addDay()->format('YYYY-M-d HH:i:s');

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

        $date = now()->addDay()->format('YYYY-M-d HH:i:s');

        $this->post( route('reminder.store', $note), [
            'time' => $date
        ]);

        $this->assertDatabaseHas('reminders', ['time' => $date]);

        $rewritten = now()->addWeek()->format('YYYY-M-d HH:i:s');
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
            'time' => now()->addDay()->format('YYYY-M-d HH:i:s'),
            'repeat' => json_encode($obj)
        ]);

        $this->assertDatabaseHas('reminders', ['repeat' => '{"every":{"number":2,"unit":"day"},"ends":{"after":3}}']);
        $this->assertEquals(3, Reminder::first()->repeat->ends->after);
        $this->assertEquals(2, Reminder::first()->repeat->every->number);
        $this->assertEquals('day', Reminder::first()->repeat->every->unit);
    }

    /*public function test_reminder_could_be_stored_for_specific_place()
    {

    }*/
}