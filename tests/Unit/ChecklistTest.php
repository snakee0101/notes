<?php

namespace Tests\Unit;

use App\Models\Checklist;
use App\Models\Note;
use Tests\TestCase;

class ChecklistTest extends TestCase
{
    public function test_a_note_has_a_checklist()
    {

    }

    public function test_a_checklist_belongs_to_note()
    {
        $checklist = Checklist::factory()->create();
        $this->assertInstanceOf(Note::class, $checklist->note);
    }

    public function test_a_checklist_has_many_tasks()
    {

    }

    public function test_a_task_belongs_to_a_checklist()
    {

    }
}
