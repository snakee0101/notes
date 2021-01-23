<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\User;
use Illuminate\Http\Request;

class CollaboratorController extends Controller
{
    public function store(User $user, Note $note)
    {
        $note->collaborators()->attach($user);
    }

    public function destroy(User $user, Note $note)
    {
        //
    }

    public function check(User $user, Note $note)
    {
        //
    }
}
