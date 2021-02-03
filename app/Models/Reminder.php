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
        'repeat' => 'object'
    ];

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

        if( is_null($this->repeat) )
            return $this->delete();

        $this->processRepeatableReminder();
    }

    public function processRepeatableReminder()
    {
        $every = $this->repeat->every;

        if(property_exists($every, 'weekdays'))
            $this->time = $this->findNearestWeekday();
         else
            $this->time = $this->time->add($every->unit, $every->number);  //TODO: add() function is a mutator (there is no need in assignment)

        $this->push();

        if( !property_exists($this->repeat, 'ends') || is_null($this->repeat->ends) )  //if execution never ends, then don't process counter and don't delete the reminder
            return;

        $this->processRepeatsCounter();
    }

    public function processRepeatsCounter()
    {
        $ends = $this->repeat->ends;
        $every = $this->repeat->every;

        if(property_exists($ends, 'after')) { //if there is occurrence counter
            $this->update([ 'repeat->ends->after' => $ends->after - 1 ]);  //TODO: use ::decrement( )
            if($this->repeat->ends->after == 0)
                $this->forceDelete();
        } else {  //if there is date restriction   //TODO: simplify with return and swapping the condition parts (-1 indent level)
            $restriction_date = Carbon::createFromTimestamp( $ends->on_date );

            if((clone $this->time)->add($every->unit, $every->number)
                          ->greaterThan( $restriction_date ))
                $this->delete();   //if the next execution date is greater than the restriction date - delete the reminder
        }
    }

    public function findNearestWeekday()
    {
        $every = $this->repeat->every;

        do {
            if($this->time->englishDayOfWeek == 'Sunday') //if sunday was not found
                $this->time->add('week', $every->number - 1);

            $nextWeekday = $this->time->addDay();
        } while ( array_search($nextWeekday->englishDayOfWeek, $this->repeat->every->weekdays) === false );
        //search until the next weekday is found in the list

        return $nextWeekday;
    }
}
