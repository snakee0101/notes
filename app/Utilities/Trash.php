<?php

namespace App\Utilities;

use App\Models\Note;

class Trash
{
    public static function removeExpired() {
         Note::onlyTrashed()->whereDate('deleted_at', '<', now()->subWeek())
             ->get()
             ->each   //each - because it should delete collaborators, images, reminders and other dependencies - with the MODEL EVENTS
             ->forceDelete();
    }
}
