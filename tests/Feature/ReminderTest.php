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
}
