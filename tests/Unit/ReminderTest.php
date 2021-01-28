<?php

namespace Tests\Unit;

use App\Models\Note;
use App\Models\Reminder;
use App\Notifications\TimeNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
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

    public function test_reminder_can_access_the_note()
    {
        $note = Note::factory()->create();
        $reminder = Reminder::factory()->create([
            'note_id' => $note->id,
            'time' => now()->addHour()
        ]);

        $this->assertInstanceOf(Note::class, $reminder->note);
    }

    public function test_reminder_sends_a_time_notification()
    {
        Notification::fake();

        $note = Note::factory()->create();
        $reminder = Reminder::factory()->create([
            'note_id' => $note->id,
            'time' => now()->addHour()
        ]);

        Notification::assertNothingSent();

        $this->travel(61)->minutes();
        $reminder->sendTimeReminder();
        Notification::assertSentTo($note->owner, TimeNotification::class);
    }

    public function test_reminders_are_sent_in_time()
    {
        Notification::fake();

        $note = Note::factory()->create();
        $reminder = Reminder::factory()->create([
            'note_id' => $note->id,
            'time' => now()->addHour()
        ]);

        Reminder::sendTimeReminders();
        Notification::assertNothingSent();

        $this->travel(61)->minutes();
        Reminder::sendTimeReminders();
        Notification::assertSentTo($note->owner, TimeNotification::class);
    }

    public function test_time_notification_sends_mail()
    {
        Notification::fake();
        Mail::fake();

        $note = Note::factory()->create();
        $reminder = Reminder::factory()->create([
            'note_id' => $note->id,
            'time' => now()->addHour()
        ]);

        $this->travel(61)->minutes();
        Reminder::sendTimeReminders();
        Notification::assertSentTo($note->owner, TimeNotification::class, function($notification, $channels, $notifiable){
            return array_search('mail', $channels) !== false;
        });
    }

    public function test_time_notification_is_broadcasted()
    {
        Notification::fake();
        Mail::fake();

        $note = Note::factory()->create();
        $reminder = Reminder::factory()->create([
            'note_id' => $note->id,
            'time' => now()->addHour()
        ]);

        $this->travel(61)->minutes();
        Reminder::sendTimeReminders();
        Notification::assertSentTo($note->owner, TimeNotification::class, function($notification, $channels, $notifiable){
            return array_search('broadcast', $channels) !== false;
        });
    }

    public function test_reminder_sends_a_location_notification()
    {

    }

    public function test_location_notification_sends_mail()
    {

    }

    public function test_location_notification_is_broadcasted()
    {

    }
}
