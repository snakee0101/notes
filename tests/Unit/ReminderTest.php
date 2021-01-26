<?php

namespace Tests\Unit;

use App\Models\Note;
use App\Models\Reminder;
use Tests\TestCase;

class ReminderTest extends TestCase
{
    public function test_a_note_has_a_reminder()
    {
        $note = Note::factory()->create();
        $reminder = Reminder::factory()->create([
            'note_id' => $note->id
        ]);

        $this->assertInstanceOf(Reminder::class, $note->reminder);
    }

    public function test_a_note_appends_reminder_to_json()
    {
        $note = Note::factory()->create();
        $reminder = Reminder::factory()->create([
            'note_id' => $note->id
        ]);

        $json_decoded = json_decode($note->toJson());
        $this->assertObjectHasAttribute('reminder_json', $json_decoded);
    }
}
