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
    public function test_a_note_has_many_reminders()
    {
        $note = Note::factory()->has( Reminder::factory()->count(3) )->create();
        $note->refresh();

        $this->assertInstanceOf(Reminder::class, $note->reminders[0]);
        $this->assertCount(3, $note->reminders);
    }

    public function test_current_user_can_see_only_their_reminder()
    {
        /*$note = Note::factory()->create();
        $reminder = Reminder::factory()->create([
            'note_id' => $note->id
        ]);

        $note->reminder;*/
    }

    public function test_a_note_appends_reminder_to_json()
    {
        $note = Note::factory()->create();
        Reminder::factory()->create([
            'note_id' => $note->id
        ]);

        $json_decoded = json_decode($note->fresh()->toJson());
        $this->assertObjectHasAttribute('reminder', $json_decoded);
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
        $this->assertEquals(now()->addDays(2)->day, $reminder->time->day);
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

        $time = now()->addDays(1)->subHour();

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

    public function test_reminder_could_process_weekdays_in_a_period_that_wider_than_a_week()
    {
        Notification::fake();

        $json = new class(){};
        $json->every = new class() {
            public $number = 2;
            public $unit = 'week';
            public $weekdays = ['Tuesday', 'Thursday'];
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
        $this->assertEquals('Thursday', $reminder->time->englishDayOfWeek);


        $before = clone $reminder->time;
        $reminder->sendTimeReminder();
        $this->assertEquals('Tuesday', $reminder->time->englishDayOfWeek);
        $this->assertEquals(2, $before->startOfWeek()->diffInWeeks((clone $reminder->time)->startOfWeek()));

        $reminder->sendTimeReminder();
        $this->assertEquals('Thursday', $reminder->time->englishDayOfWeek);
    }

    public function test_reminder_could_handle_special_case_with_sunday()
    {
        Notification::fake();

        $json = new class(){};
        $json->every = new class() {
            public $number = 2;
            public $unit = 'week';
            public $weekdays = ['Sunday'];
        };

        $time = now()->weekday(1);  //1 is Monday

        $note = Note::factory()->create();
        $reminder = Reminder::factory()->create([
            'note_id' => $note->id,
            'time' => $time,
            'repeat' => $json
        ]);

        $this->assertDatabaseCount('reminders', 1);

        $reminder->sendTimeReminder();
        $this->assertEquals('Sunday', $reminder->time->englishDayOfWeek);
        $before = clone $reminder->time;

        $reminder->sendTimeReminder();
        $this->assertEquals('Sunday', $reminder->time->englishDayOfWeek);
        $this->assertEquals(2, $before->startOfWeek()->diffInWeeks((clone $reminder->time)->startOfWeek()));
    }

    public function test_reminder_should_process_nearest_weekday_as_a_restriction_date()
    {
        Notification::fake();

        $restriction =  now()->weekday(4);  //4 is Thursday

        $json = new class(){};
        $json->every = new class() {
            public $number = 1;
            public $unit = 'week';
            public $weekdays = ['Wednesday', 'Friday'];
        };

        $json->ends =  new class($restriction) {
            public $on_date;
            public function __construct($on_date) {
                $this->on_date = $on_date;
            }
        };

        $time = now()->weekday(1);  //1 is Monday

        $note = Note::factory()->create();
        $reminder = Reminder::factory()->create([
            'note_id' => $note->id,
            'time' => $time,
            'repeat' => $json
        ]);

        $this->assertDatabaseCount('reminders', 1);

        $reminder->sendTimeReminder();
        $this->assertEquals('Wednesday', $reminder->time->englishDayOfWeek);

        $reminder->sendTimeReminder();  //if next date (Friday) is unreachable, then delete the reminder
        $this->assertDatabaseCount('reminders', 0);
    }

    public function test_findNextWeekday_function_doesnt_mutates_a_date_and_returns_nearest_weekday()
    {
        Notification::fake();

        $json = new class(){};
        $json->every = new class() {
            public $number = 1;
            public $unit = 'week';
            public $weekdays = ['Wednesday', 'Friday'];
        };

        $time = now()->weekday(1);  //1 is Monday

        $note = Note::factory()->create();
        $reminder = Reminder::factory()->create([
            'note_id' => $note->id,
            'time' => $time,
            'repeat' => $json
        ]);

        $before = $reminder->time->timestamp;
        $this->assertInstanceOf(Carbon::class, $reminder->findNearestWeekday());
        $after = $reminder->fresh()->time->timestamp;

        $this->assertEquals($before, $after);
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
