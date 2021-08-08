<?php

namespace App\Providers;

use App\Models\Note;
use App\Policies\NotePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
         Note::class => NotePolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();

        Gate::define('sync_collaborator', fn($user, Note $note) => $user->is($note->owner) );
        Gate::define('image_manipulation', fn($user, Note $note) => $user->is($note->owner) || $note->collaborators()->where('users.id', $user->id)->exists() );
        Gate::define('link_manipulation', fn($user, Note $note) => $user->is($note->owner) || $note->collaborators()->where('users.id', $user->id)->exists() );
    }
}
