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
            $this->delete();
        else
            $this->processRepeatableReminder();
    }

    public function processRepeatableReminder()
    {
        $every = $this->repeat->every;
        $this->time = $this->time->add($every->unit, $every->number);
        $this->push();

        if( !property_exists($this->repeat, 'ends') || is_null($this->repeat->ends) )  //if execution never ends, then don't process counter and don't delete the reminder
            return;

        $this->processRepeatsCounter();
    }

    public function processRepeatsCounter()
    {
        $ends = $this->repeat->ends;

        if($ends->after) { //if there is occurrence counter
            $this->update([ 'repeat->ends->after' => $ends->after - 1 ]);
            if($this->repeat->ends->after == 0)
                return $this->forceDelete();
        } else {  //if there is date restriction
            $restriction_date = Carbon::createFromTimestamp( $ends->on_date );

            if($this->time->greaterThan( $restriction_date ))
                $this->delete();   //if the next execution date is greater than the restriction date - delete the reminder
        }

        @$this->push();
    }
}
