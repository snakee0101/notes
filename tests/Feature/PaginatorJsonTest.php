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
    protected function test_paginator($other_notes, $pinned_notes, $route)
    {
        $json = $this->getJson( $route . '?page=1&notes_type=other_notes' )->json();

        $this->assertNotEmpty( array_filter($json['data'], fn($item) => $item["header"] == $other_notes->first()->header ) );
        $this->assertEmpty( array_filter($json['data'], fn($item) => $item["header"] == $other_notes->last()->header ) );

        $json_page_2 = $this->getJson( $route . '?page=2&notes_type=other_notes' )->json();
        $this->assertNotEmpty( array_filter($json_page_2['data'], fn($item) => $item["header"] == $other_notes->last()->header ) );
        $this->assertEmpty( array_filter($json_page_2['data'], fn($item) => $item["header"] == $other_notes->first()->header ) );

        //CHECK FOR PINNED NOTES

        $json_pinned = $this->getJson( $route . '?page=1&notes_type=pinned_notes' )->json();

        $this->assertNotEmpty( array_filter($json_pinned['data'], fn($item) => $item["header"] == $pinned_notes->first()->header ) );
        $this->assertEmpty( array_filter($json_pinned['data'], fn($item) => $item["header"] == $pinned_notes->last()->header ) );

        $json_pinned_page_2 = $this->getJson( $route . '?page=2&notes_type=pinned_notes' )->json();
        $this->assertNotEmpty( array_filter($json_pinned_page_2['data'], fn($item) => $item["header"] == $pinned_notes->last()->header ) );
        $this->assertEmpty( array_filter($json_pinned_page_2['data'], fn($item) => $item["header"] == $pinned_notes->first()->header ) );
    }

    public function test_main_page_paginator()
    {
        auth()->login( $owner = User::factory()->create() );

        $other_notes = Note::factory()->for($owner,'owner')
                                ->count(40)
                                ->create(['pinned' => false]);

        $pinned_notes = Note::factory()->for($owner,'owner')
            ->count(40)
            ->create(['pinned' => true]);

        $this->test_paginator($other_notes, $pinned_notes, route('notes'));
    }

    public function test_tag_page_paginator()
    {
        auth()->login( $owner = User::factory()->create() );

        $tag = Tag::factory()->for($owner,'owner')->create();
        $pinned_notes = Note::factory()->for($owner,'owner')
            ->hasAttached($tag)
            ->count(40)
            ->create(['pinned' => true]);

        $other_notes = Note::factory()->for($owner,'owner')
            ->hasAttached($tag)
            ->count(40)
            ->create(['pinned' => false]);

        $this->test_paginator($other_notes, $pinned_notes, route('tag.show',$tag->name));
    }

    public function test_reminder_page_paginator()
    {
        auth()->login( $owner = User::factory()->create() );

        $pinned_notes = Note::factory()->for($owner,'owner')
            ->has(Reminder::factory(['user_id' => $owner->id]), 'reminders')
            ->count(40)
            ->create(['pinned' => true]);

        $other_notes = Note::factory()->for($owner,'owner')
            ->has(Reminder::factory(['user_id' => $owner->id]), 'reminders')
            ->count(40)
            ->create(['pinned' => false]);

        $this->test_paginator($other_notes, $pinned_notes, route('reminder.index'));
    }

    public function test_archive_page_paginator()
    {
        auth()->login( $owner = User::factory()->create() );

        $other_notes = Note::factory(['archived' => true])->for($owner,'owner')
            ->count(40)
            ->create(['pinned' => false]);

        $pinned_notes = Note::factory(['archived' => true])->for($owner,'owner')
            ->count(40)
            ->create(['pinned' => true]);

        $this->test_paginator($other_notes, $pinned_notes, route('archive'));
    }

    public function test_trash_paginator()
    {
        auth()->login($owner = User::factory()->create());

        $other_notes = Note::factory()->for($owner,'owner')
            ->count(40)
            ->create(['pinned' => false]);

        $pinned_notes = Note::factory()->for($owner,'owner')
            ->count(40)
            ->create(['pinned' => true]);

        $other_notes->each->delete();
        $pinned_notes->each->delete();

        $this->assertNotEmpty(Note::onlyTrashed()->get());

        $this->test_paginator($other_notes, $pinned_notes, route('trash'));
    }
}
