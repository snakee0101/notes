<?php

namespace Tests\Unit;

use App\Models\Link;
use App\Models\Note;
use Tests\TestCase;

class LinkTest extends TestCase
{
    public function test_a_note_has_many_links()
    {

    }

    public function test_a_link_belongs_to_the_note()
    {
        $note = Note::factory()->create();
        $link = Link::factory()->create(['note_id' => $note->id]);

        $this->assertInstanceOf(Note::class, $link->note);
    }
}
