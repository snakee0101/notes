<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Utilities\Trash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TrashTest extends TestCase
{
    public function test_a_user_can_empty_the_trash()
    {
        Note::factory()->count(5)->create()->each->delete();
        $this->assertCount(5, Note::onlyTrashed()->get());

        $this->delete(route('trash.empty'));
        $this->assertEmpty( Note::onlyTrashed()->get() );
    }
}
