<?php

namespace App\Models;

use App\Notifications\TimeNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;

class Reminder extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = [];

    public function note()
    {
        return $this->belongsTo(Note::class);
    }

    public static function sendExpired()
    {
        self::sendTimeReminders();
        self::sendLocationReminders();
    }

    public static function sendTimeReminders()
    {
        self::whereNotNull('time')
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

        //TODO: send the reminder and delete it if it is not repeated
        //TODO: if it is repeated, change the time to the repeat time and (if needed) decrease the repeats counter
    }
}
