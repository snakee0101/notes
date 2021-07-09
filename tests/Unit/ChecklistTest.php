<?php

namespace Tests\Unit;

use App\Models\Checklist;
use App\Models\Note;
use App\Models\Task;
use Tests\TestCase;

class ChecklistTest extends TestCase
{
    public function test_a_note_has_a_checklist()
    {
        $note = Note::factory()->has(Checklist::factory())->create();
        $this->assertInstanceOf(Checklist::class, $note->checklist);
    }

    public function test_a_checklist_belongs_to_note()
    {
        $checklist = Checklist::factory()->create();
        $this->assertInstanceOf(Note::class, $checklist->note);
    }

    public function test_a_checklist_has_many_tasks()
    {
        $checklist = Checklist::factory()->create();
        $tasks = Task::factory()->for($checklist)->count(3)->create();

        $this->assertDatabaseCount('tasks', 3);
        $this->assertInstanceOf(Task::class, $checklist->fresh()->tasks[0]);
    }

    public function test_a_task_belongs_to_a_checklist()
    {
        $checklist = Checklist::factory()->create();
        $tasks = Task::factory()->for($checklist)->count(3)->create();

        $this->assertInstanceOf(Checklist::class, $tasks[0]->checklist);
    }

    public function test_a_checklist_could_be_parsed_to_an_array()
    {
        $trix_output = "<div><!--block--><b>some task 1</b><br>second task<br><i>another <b>task</b></i></div>";

        $result = Checklist::parse($trix_output);

        $expected = ["some task 1", "second task", "another task"];

        $this->assertEquals($expected, $result);
    }
}
