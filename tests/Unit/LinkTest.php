<?php

namespace Tests\Unit;

use App\Models\Link;
use App\Models\Note;
use Tests\TestCase;

class LinkTest extends TestCase
{
    public function test_a_note_has_many_links()
    {
        $note = Note::factory()->create();
        Link::factory()->count(3)->create(['note_id' => $note->id]);
        $note->refresh();

        $this->assertInstanceOf(Link::class, $note->links()->first());
        $this->assertCount(3, $note->links);
    }

    public function test_a_link_belongs_to_the_note()
    {
        $note = Note::factory()->create();
        $link = Link::factory()->create(['note_id' => $note->id]);

        $this->assertInstanceOf(Note::class, $link->note);
    }

    public function test_a_the_links_are_automatically_deleted_when_the_note_is_force_deleted()
    {
        $note = Note::factory()->create();
        Link::factory()->count(3)->create(['note_id' => $note->id]);
        $note->refresh();

        $this->assertDatabaseCount('links', 3);

        $note->forceDelete();

        $this->assertDatabaseCount('links', 0);
    }
}
