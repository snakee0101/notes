<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReminderController extends Controller
{
    public function index()
    {
        $data = [
            'pinned_notes' => auth()->user()->notes()->has('reminder')->where('pinned', true)->paginate(),
            'other_notes' => auth()->user()->notes()->has('reminder')->where('pinned', false)->paginate()
        ];

        if(! request()->wantsJson()) {
            $data['pinned_notes'] = $data['pinned_notes']->toJson();
            $data['other_notes'] = $data['other_notes']->toJson();

            return view('reminders', $data);
        }

        return $data[ request('notes_type') ];
    }

    public function store(Request $request, Note $note)
    {
        abort_if(Gate::denies('reminders', $note), 403, 'Only owner and collaborators can manipulate reminders');

        return $note->reminders()->updateOrCreate([
            'note_id' => $note->id,
            'user_id' => auth()->id(),
        ], [
            'time' => request('time'),
            'repeat' => json_decode( request('repeat') )
        ]);
    }

    public function destroy(Note $note)
    {
        abort_if(Gate::denies('reminders', $note), 403, 'Only reminder owner can delete reminder');

        $note->reminders()->firstWhere('user_id', auth()->id())
                          ?->delete();
    }
}
