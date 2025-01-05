<?php

namespace App\Http\Controllers;

use App\Actions\NoteIndexAction;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReminderController extends Controller
{
    public function index(NoteIndexAction $index)
    {
        return $index->getNotes( request(), 'reminders', auth()->user()->notes()->whereHas('reminders', function($q){
            $q->where('reminders.user_id', auth()->id());
        }) );
    }

    public function store(Request $request, Note $note)
    {
        abort_if(Gate::denies('reminders', $note), 403, 'Only owner and collaborators can manipulate reminders');

        //to avoid timezone conflict - all dates are normalized to UTC
        $utc_date = new \DateTime($request->input('time'), new \DateTimeZone($request->input('client_timezone')));
        $utc_date->setTimezone(new \DateTimeZone('UTC'));

        return $note->reminders()->updateOrCreate([
            'note_id' => $note->id,
            'user_id' => auth()->id(),
        ], [
            'time' => $utc_date,
            'repeat' => request('repeat') ? json_decode(request('repeat')) : new class(){}
        ]);
    }

    public function destroy(Note $note)
    {
        abort_if(Gate::denies('reminders', $note), 403, 'Only reminder owner can delete reminder');

        $note->reminders()->firstWhere('user_id', auth()->id())
                          ?->delete();
    }
}
