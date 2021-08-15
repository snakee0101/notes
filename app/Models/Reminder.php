<?php

namespace App\Models;

use App\Notifications\TimeNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;

class Reminder extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = [];
    protected $casts = [
        'repeat' => 'object',
    ];
    protected $dates = ['time'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function note()
    {
        return $this->belongsTo(Note::class);
    }

    public static function sendExpired()
    {
        static::sendTimeReminders();
        static::sendLocationReminders();
    }

    public static function sendTimeReminders()
    {
        static::whereNotNull('time')
            ->where('time', '<', now())
            ->get()
            ->each
            ->sendTimeReminder();
    }

    public static function sendLocationReminders()
    {

    }

    public function sendTimeReminder()
    {
        Notification::send($this->note->owner, new TimeNotification($this->note));

        if (is_null($this->repeat))
            return $this->delete();

        $this->processRepeatableReminder();
    }

    public function processRepeatableReminder()
    {
        $every = $this->repeat->every;

        if (@$every->weekdays)
            $newTime = $this->findNearestWeekday();
        else
            $newTime = $this->time->add($every->unit, $every->number);

        $this->update(['time' => $newTime]);

        if (@!$this->repeat->ends)  //if execution never ends, then don't process counter and don't delete the reminder
            return;

        $this->processRepeatsCounter();
    }

    public function processRepeatsCounter()
    {
        $ends = $this->repeat->ends;
        $every = $this->repeat->every;

        if (@$ends->after) { //if there is occurrence counter
            $this->update(['repeat->ends->after' => $ends->after - 1]);
            if ($this->repeat->ends->after == 0)
                $this->forceDelete();
            return;
        }

        //if there is date restriction
        $restriction_date = Carbon::createFromTimestamp($ends->on_date);

        if (@$every->weekdays) {
            if ($this->findNearestWeekday()->greaterThan($restriction_date))
                $this->delete();

            return;
        }

        $next_execution_date = (clone $this->time)->add($every->unit, $every->number);
        if ($next_execution_date->greaterThan($restriction_date))
            $this->delete();
    }

    public function findNearestWeekday()
    {
        $time = clone $this->time;
        $every = $this->repeat->every;

        do {
            if ($time->englishDayOfWeek == 'Sunday') //if sunday was not found
                $time->add('week', $every->number - 1);

            $time->addDay();
        } while (array_search($time->englishDayOfWeek, $every->weekdays) === false);
        //search until the next weekday is found in the list

        return $time;
    }
}
