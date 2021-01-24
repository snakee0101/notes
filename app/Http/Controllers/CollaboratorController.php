<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\User;
use Illuminate\Http\Request;

class CollaboratorController extends Controller
{
    public function sync(Note $note)
    {
        $emails = request('emails');

        $note->collaborators()->sync(
            User::whereIn('email', $emails)->pluck('id')
        );
        //TODO: Send mail to the user when it is added or deleted from collaborators
    }

    public function check($email)
    {
        return response()->json([
            'exists' => User::whereEmail($email)->exists()
        ]);
    }
}
