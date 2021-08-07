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

    public function view(User $user, Note $note) //owner and collaborators are allowed
    {
        return $user->is($note->owner) || $note->collaborators()->where('users.id', $user->id)->exists();
    }

    public function update(User $user, Note $note) //owner and collaborators are allowed
    {
        return $user->is($note->owner) || $note->collaborators()->where('users.id', $user->id)->exists();
    }

    public function updateArchived(User $user, Note $note)
    {
        return $user->is($note->owner);
    }

    public function delete(User $user, Note $note)
    {
        return $user->is($note->owner);
    }

    public function restore(User $user, Note $note)
    {
        return $user->is($note->owner);
    }

    public function duplicate(User $user, Note $note)
    {
        return $user->is($note->owner);
    }
}
