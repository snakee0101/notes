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

        $userIds = [];
        foreach($emails as $email)
        {
            if(User::whereEmail($email)->exists())
                $userIds[] = User::whereEmail($email)->value('id');
        }

        $note->collaborators()->sync($userIds);
    }

    public function check($email)
    {
        return response()->json([
            'exists' => User::whereEmail($email)->exists()
        ]);
    }
}
