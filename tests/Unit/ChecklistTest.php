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
}
