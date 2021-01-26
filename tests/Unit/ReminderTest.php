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
}
