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

    public function test_all_tasks_could_be_unchecked()
    {
        $checklist = Checklist::factory()->create();

        Task::factory()->for($checklist)->count(3)->create([ 'completed' => false ]);
        Task::factory()->for($checklist)->count(3)->create([ 'completed' => true  ]);

        $checklist->uncheckAll();

        $this->assertCount(6, Task::where('completed', false)->get());
    }

    public function test_all_completed_tasks_could_be_removed()
    {
        $checklist = Checklist::factory()->create();

        Task::factory()->for($checklist)->count(3)->create([ 'completed' => false ]);
        Task::factory()->for($checklist)->count(5)->create([ 'completed' => true  ]);

        $checklist->removeCompleted();

        $this->assertDatabaseCount('tasks', 3);
    }

    public function test_checklist_could_be_converted_to_HTML()
    {
        $checklist = Checklist::factory()->create();
        $tasks = Task::factory()->for($checklist)->count(3)->create();

        $parsed = $checklist->toHTML();

        $expected = $tasks[0]->text . '<br>' . $tasks[1]->text . '<br>' . $tasks[2]->text . '<br>';
        $this->assertEquals($expected, $parsed);
    }

    public function test_checklist_could_wrap_tasks_data_into_collection()
    {
        $checklist = Checklist::factory()->create();

        $tasks_data = [ ['text' => 'some task 1', 'completed' => true],
                   ['text' => 'second task', 'completed' => false],
                   ['text' => 'another task', 'completed' => true] ];

        $wrapped = Checklist::wrap($tasks_data);

        $this->assertEquals('some task 1', $wrapped[0]['text']);
        $this->assertEquals('second task', $wrapped[1]['text']);
        $this->assertEquals('another task', $wrapped[2]['text']);

        $this->assertTrue($wrapped[0]['completed']);
        $this->assertFalse($wrapped[1]['completed']);
        $this->assertTrue($wrapped[2]['completed']);

        $this->assertEquals(1, $wrapped[0]['position']);
        $this->assertEquals(2, $wrapped[1]['position']);
        $this->assertEquals(3, $wrapped[2]['position']);
    }
}
