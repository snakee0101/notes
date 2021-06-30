<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\Reminder;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaginatorJsonTest extends TestCase
{
    public function test_main_page_paginator()
    {
        $owner = User::factory()->create();
        $notes = Note::factory()->for($owner,'owner')
                                ->count(40)
                                ->create();

        auth()->login($owner);

        $http_response = $this->getJson( route('notes') . '?page=1&notes_type=other_notes' );
        $this->assertJson($http_response->content());

        $this->assertStringContainsString("\"header\":\"{$notes->first()->header}\"", $http_response->content());
        $this->assertStringNotContainsString("\"header\":\"{$notes->last()->header}\"", $http_response->content());


        $http_response = $this->getJson( route('notes') . '?page=2&notes_type=other_notes' );
        $this->assertJson($http_response->content());

        $this->assertStringContainsString("\"header\":\"{$notes->last()->header}\"", $http_response->content());
        $this->assertStringNotContainsString("\"header\":\"{$notes->first()->header}\"", $http_response->content());


        //CHECK FOR PINNED NOTES
        $notes->toQuery()->update(['pinned' => true]);
        $notes = Note::all();

        $http_response = $this->getJson( route('notes') . '?page=1&notes_type=pinned_notes' );
        $this->assertJson($http_response->content());

        $this->assertStringContainsString("\"header\":\"{$notes->first()->header}\"", $http_response->content());
        $this->assertStringNotContainsString("\"header\":\"{$notes->last()->header}\"", $http_response->content());


        $http_response = $this->getJson( route('notes') . '?page=2&notes_type=pinned_notes' );
        $this->assertJson($http_response->content());

        $this->assertStringContainsString("\"header\":\"{$notes->last()->header}\"", $http_response->content());
        $this->assertStringNotContainsString("\"header\":\"{$notes->first()->header}\"", $http_response->content());
    }

    public function test_tag_page_paginator()
    {
        $owner = User::factory()->create();
        $tag = Tag::factory()->for($owner,'owner')->create();
        $notes = Note::factory()->for($owner,'owner')
            ->hasAttached($tag)
            ->count(40)
            ->create();

        auth()->login($owner);

        $http_response = $this->getJson( route('tag.show',$tag->name) . '?page=1&notes_type=other_notes' );
        $this->assertJson($http_response->content());

        $this->assertStringContainsString("\"header\":\"{$notes->first()->header}\"", $http_response->content());
        $this->assertStringNotContainsString("\"header\":\"{$notes->last()->header}\"", $http_response->content());


        $http_response = $this->getJson( route('tag.show',$tag->name) . '?page=2&notes_type=other_notes' );
        $this->assertJson($http_response->content());

        $this->assertStringContainsString("\"header\":\"{$notes->last()->header}\"", $http_response->content());
        $this->assertStringNotContainsString("\"header\":\"{$notes->first()->header}\"", $http_response->content());

        //CHECK FOR PINNED NOTES
        $notes->toQuery()->update(['pinned' => true]);
        $notes = Note::all();

        $http_response = $this->getJson( route('tag.show',$tag->name) . '?page=1&notes_type=pinned_notes' );
        $this->assertJson($http_response->content());

        $this->assertStringContainsString("\"header\":\"{$notes->first()->header}\"", $http_response->content());
        $this->assertStringNotContainsString("\"header\":\"{$notes->last()->header}\"", $http_response->content());


        $http_response = $this->getJson( route('tag.show',$tag->name) . '?page=2&notes_type=pinned_notes' );
        $this->assertJson($http_response->content());

        $this->assertStringContainsString("\"header\":\"{$notes->last()->header}\"", $http_response->content());
        $this->assertStringNotContainsString("\"header\":\"{$notes->first()->header}\"", $http_response->content());
    }

    public function test_reminder_page_paginator()
    {
        $owner = User::factory()->create();
        $notes = Note::factory()->for($owner,'owner')
            ->has(Reminder::factory())
            ->count(40)
            ->create();

        auth()->login($owner);

        $http_response = $this->getJson( route('reminder.index') . '?page=1&notes_type=other_notes' );
        $this->assertJson($http_response->content());

        $this->assertStringContainsString("\"header\":\"{$notes->first()->header}\"", $http_response->content());
        $this->assertStringNotContainsString("\"header\":\"{$notes->last()->header}\"", $http_response->content());


        $http_response = $this->getJson( route('reminder.index') . '?page=2&notes_type=other_notes' );
        $this->assertJson($http_response->content());

        $this->assertStringContainsString("\"header\":\"{$notes->last()->header}\"", $http_response->content());
        $this->assertStringNotContainsString("\"header\":\"{$notes->first()->header}\"", $http_response->content());


        //CHECK FOR PINNED NOTES
        $notes->toQuery()->update(['pinned' => true]);
        $notes = Note::all();

        $http_response = $this->getJson( route('reminder.index') . '?page=1&notes_type=pinned_notes' );
        $this->assertJson($http_response->content());

        $this->assertStringContainsString("\"header\":\"{$notes->first()->header}\"", $http_response->content());
        $this->assertStringNotContainsString("\"header\":\"{$notes->last()->header}\"", $http_response->content());


        $http_response = $this->getJson( route('reminder.index') . '?page=2&notes_type=pinned_notes' );
        $this->assertJson($http_response->content());

        $this->assertStringContainsString("\"header\":\"{$notes->last()->header}\"", $http_response->content());
        $this->assertStringNotContainsString("\"header\":\"{$notes->first()->header}\"", $http_response->content());
    }

    public function test_archive_page_paginator()
    {
        $owner = User::factory()->create();
        $notes = Note::factory(['archived' => true])->for($owner,'owner')
            ->count(40)
            ->create();

        auth()->login($owner);

        $http_response = $this->getJson( route('archive') . '?page=1&notes_type=other_notes' );
        $this->assertJson($http_response->content());

        $this->assertStringContainsString("\"header\":\"{$notes->first()->header}\"", $http_response->content());
        $this->assertStringNotContainsString("\"header\":\"{$notes->last()->header}\"", $http_response->content());


        $http_response = $this->getJson( route('archive') . '?page=2&notes_type=other_notes' );
        $this->assertJson($http_response->content());

        $this->assertStringContainsString("\"header\":\"{$notes->last()->header}\"", $http_response->content());
        $this->assertStringNotContainsString("\"header\":\"{$notes->first()->header}\"", $http_response->content());


        //CHECK FOR PINNED NOTES
        $notes->toQuery()->update(['pinned' => true]);
        $notes = Note::withArchived()->get();

        $http_response = $this->getJson( route('archive') . '?page=1&notes_type=pinned_notes' );
        $this->assertJson($http_response->content());

        $this->assertStringContainsString("\"header\":\"{$notes->first()->header}\"", $http_response->content());
        $this->assertStringNotContainsString("\"header\":\"{$notes->last()->header}\"", $http_response->content());


        $http_response = $this->getJson( route('archive') . '?page=2&notes_type=pinned_notes' );
        $this->assertJson($http_response->content());

        $this->assertStringContainsString("\"header\":\"{$notes->last()->header}\"", $http_response->content());
        $this->assertStringNotContainsString("\"header\":\"{$notes->first()->header}\"", $http_response->content());
    }

    public function test_trash_paginator()
    {
        $owner = User::factory()->create();
        $notes = Note::factory()->for($owner,'owner')
            ->count(40)
            ->create();

        $notes->each->delete();

        $this->assertNotEmpty(Note::onlyTrashed()->get());

        auth()->login($owner);

        $http_response = $this->getJson( route('trash') . '?page=1&notes_type=other_notes' );
        $this->assertJson($http_response->content());

        $this->assertStringContainsString("\"header\":\"{$notes->first()->header}\"", $http_response->content());
        $this->assertStringNotContainsString("\"header\":\"{$notes->last()->header}\"", $http_response->content());


        $http_response = $this->getJson( route('trash') . '?page=2&notes_type=other_notes' );
        $this->assertJson($http_response->content());

        $this->assertStringContainsString("\"header\":\"{$notes->last()->header}\"", $http_response->content());
        $this->assertStringNotContainsString("\"header\":\"{$notes->first()->header}\"", $http_response->content());


        //CHECK FOR PINNED NOTES
        $notes->toQuery()->update(['pinned' => true]);
        $notes = Note::onlyTrashed()->get();

        $http_response = $this->getJson( route('trash') . '?page=1&notes_type=pinned_notes' );
        $this->assertJson($http_response->content());

        $this->assertStringContainsString("\"header\":\"{$notes->first()->header}\"", $http_response->content());
        $this->assertStringNotContainsString("\"header\":\"{$notes->last()->header}\"", $http_response->content());


        $http_response = $this->getJson( route('trash') . '?page=2&notes_type=pinned_notes' );
        $this->assertJson($http_response->content());

        $this->assertStringContainsString("\"header\":\"{$notes->last()->header}\"", $http_response->content());
        $this->assertStringNotContainsString("\"header\":\"{$notes->first()->header}\"", $http_response->content());
    }
}
