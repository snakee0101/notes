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

    public function test_reminder_could_have_repeat_status()
    {

    }

    public function test_reminder_could_be_stored_for_specific_place()
    {

    }
}
