<?php

namespace Tests\Unit;

use App\Models\Note;
use App\Models\Reminder;
use App\Notifications\TimeNotification;
use Carbon\Carbon;
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

    public function test_reminder_could_send_expired_reminders()
    {
        Notification::fake();

        $note = Note::factory()->create();
        $reminder = Reminder::factory()->create([
            'note_id' => $note->id,
            'time' => now()->addHour()
        ]);

        Notification::assertNothingSent();
        $this->travel(61)->minutes();
        Reminder::sendExpired();
        Notification::assertSentTo($note->owner, TimeNotification::class);
    }

    public function test_reminder_is_deleted_after_sending()
    {
        Notification::fake();

        $note = Note::factory()->create();
        $reminder = Reminder::factory()->create([
            'note_id' => $note->id,
            'time' => now()->addHour()
        ]);

        $this->assertDatabaseCount('reminders', 1);

        $reminder->sendTimeReminder();
        $this->assertDatabaseCount('reminders', 0);
    }

    public function test_repeat_status_is_json_object()
    {
        $json = new class(){};
        $json->every = new class(){
            public $number = 2;
            public $unit = 'day';
        };

        $note = Note::factory()->create();
        $reminder = Reminder::factory()->create([
            'note_id' => $note->id,
            'time' => now()->addHour(),
            'repeat' => $json
        ]);

        $this->assertIsObject($reminder->repeat);
        $this->assertObjectHasAttribute('number', $reminder->repeat->every);
        $this->assertObjectHasAttribute('unit', $reminder->repeat->every);
    }

    public function test_reminder_time_is_Carbon_instance()
    {
        $note = Note::factory()->create();
        $reminder = Reminder::factory()->for($note)->create([
            'time' => now()->addHour()
        ]);

        $this->assertInstanceOf(Carbon::class, $reminder->time);
    }

    public function test_reminder_resets_next_execution_date_after_the_repeat()
    {
        Notification::fake();

        $json = new class(){};
        $json->every = new class() {
                public $number = 2;
                public $unit = 'day';
        };

        $time = now()->addHour();

        $note = Note::factory()->create();
        $reminder = Reminder::factory()->create([
            'note_id' => $note->id,
            'time' => $time,
            'repeat' => $json
        ]);

        $this->assertEquals($time->timestamp, $reminder->time->timestamp);

        $reminder->sendTimeReminder();
        $this->assertEquals($time->addDays(2)->timestamp, $reminder->time->timestamp);
    }

    public function test_if_reminder_never_ends_then_repeat_status_doesnt_change()
    {
        Notification::fake();

        $json = new class(){};
        $json->every = new class() {
            public $number = 2;
            public $unit = 'day';
        };

        $time = now()->addHour();

        $note = Note::factory()->create();
        $reminder = Reminder::factory()->create([
            'note_id' => $note->id,
            'time' => $time,
            'repeat' => $json
        ]);

        $repeat_status = $reminder->repeat;
        $reminder->sendTimeReminder();
        $this->assertEquals(json_encode($repeat_status), json_encode($reminder->fresh()->repeat));
    }

    public function test_reminder_decrements_repeat_counter()
    {
        Notification::fake();

        $json = new class(){};
        $json->every = new class() {
            public $number = 1;
            public $unit = 'day';
        };
        $json->ends = new class() {
            public $after = 5;
        };

        $time = now()->addHour();

        $note = Note::factory()->create();
        $reminder = Reminder::factory()->create([
            'note_id' => $note->id,
            'time' => $time,
            'repeat' => $json
        ]);

        $this->assertEquals(5, $reminder->repeat->ends->after);
        $reminder->sendTimeReminder();
        $this->assertEquals(4, $reminder->fresh()->repeat->ends->after);
    }

    public function test_repeated_reminder_is_deleted_only_after_the_last_occurrence()
    {
        Notification::fake();

        $json = new class(){};
        $json->every = new class() {
            public $number = 1;
            public $unit = 'day';
        };
        $json->ends = new class() {
            public $after = 1;
        };

        $time = now()->addHour();

        $note = Note::factory()->create();
        $reminder = Reminder::factory()->create([
            'note_id' => $note->id,
            'time' => $time,
            'repeat' => $json
        ]);

        $reminder->sendTimeReminder();
        $this->assertDatabaseCount('reminders', 0);
    }

    public function test_repeated_reminder_is_deleted_only_after_the_last_date_repeat()
    {
        Notification::fake();

        $json = new class(){};
        $json->every = new class() {
            public $number = 1;
            public $unit = 'day';
        };

        $time = now()->addDays(2)->subHour();

        $json->ends = new class($time) {
            public $on_date;
            public function __construct($time) {
                $this->on_date = $time->timestamp;
            }
        };

        $note = Note::factory()->create();
        $reminder = Reminder::factory()->create([
            'note_id' => $note->id,
            'time' => now(),
            'repeat' => $json
        ]);

        $this->assertDatabaseCount('reminders', 1);
        $reminder->sendTimeReminder();
        $this->assertDatabaseCount('reminders', 0);
    }

    public function test_when_reminder_runs_it_sets_next_execution_date_to_the_nearest_weekday()
    {
        Notification::fake();

        $json = new class(){};
        $json->every = new class() {
            public $number = 1;
            public $unit = 'week';
            public $weekdays = ['Tuesday', 'Saturday'];
        };

        $time = now()->weekday(0);  //0 is Sunday

        $note = Note::factory()->create();
        $reminder = Reminder::factory()->create([
            'note_id' => $note->id,
            'time' => $time,
            'repeat' => $json
        ]);

        $this->assertDatabaseCount('reminders', 1);

        $reminder->sendTimeReminder();
        $this->assertEquals('Tuesday', $reminder->time->englishDayOfWeek);

        $reminder->sendTimeReminder();
        $this->assertEquals('Saturday', $reminder->time->englishDayOfWeek);
    }

    /*public function test_reminder_sends_a_location_notification()
    {

    }

    public function test_location_notification_sends_mail()
    {

    }

    public function test_location_notification_is_broadcasted()
    {

    }*/
}
