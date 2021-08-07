<?php

namespace App\Policies;

use App\Models\Note;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        //
    }

    public function view(User $user, Note $note)
    {
        //
    }

    public function update(User $user, Note $note)
    {
        //
    }

    public function delete(User $user, Note $note)
    {
        return $user->is($note->owner);
    }

    public function restore(User $user, Note $note)
    {
        //
    }

    public function duplicate(User $user, Note $note)
    {
        return $user->is($note->owner);
    }
}
