<?php

namespace Tests\Unit;

use App\Models\Note;
use App\Utilities\Trash;
use Tests\TestCase;

class TrashTest extends TestCase
{
    public function test_remove_expired()
    {
        Note::factory()->create()
                       ->delete();

        $this->travelTo( now()->subWeek()->subDay() );
        Note::factory()->create()
                        ->delete();

        $this->assertCount(2, Note::onlyTrashed()->get());

        $this->travelBack();
        Trash::removeExpired();
        $this->assertCount(1, Note::onlyTrashed()->get());
    }

    public function test_empty() {
        Note::factory()->count(5)->create()->each->delete();
        $this->assertCount(5, Note::onlyTrashed()->get());

        Trash::empty();
        $this->assertEmpty( Note::onlyTrashed()->get() );
    }
}
