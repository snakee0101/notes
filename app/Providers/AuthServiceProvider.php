<?php

namespace App\Providers;

use App\Models\Note;
use App\Models\Tag;
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

        Gate::define('update_tags', fn($user, Tag $tag) => $user->is($tag->owner) );
        Gate::define('sync_collaborator', fn($user, Note $note) => $user->is($note->owner) );
        Gate::define('checklist', fn($user, Note $note) => $user->is($note->owner) || $note->collaborators()->where('users.id', $user->id)->exists() );
        Gate::define('image_manipulation', fn($user, Note $note) => $user->is($note->owner) || $note->collaborators()->where('users.id', $user->id)->exists() );
        Gate::define('link_manipulation', fn($user, Note $note) => $user->is($note->owner) || $note->collaborators()->where('users.id', $user->id)->exists() );
    }
}
