<?php

namespace Tests\Feature;

use App\Models\Checklist;
use App\Models\Note;
use App\Models\Task;
use App\Models\User;
use Database\Factories\NoteFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ChecklistTest extends TestCase
{
    public function test_all_checklist_tasks_could_be_saved_at_once()
    {
        $user = User::factory()->create();
        auth()->login( $user );

        $note = Note::factory()->for($user, 'owner')->create();

        $this->post( route('checklist.store'), [
            'checklist_data' => [
                ['text' => 'some task 1',
                 'completed' => true],
                ['text' => 'second task',
                 'completed' => false],
                ['text' => 'another task',
                 'completed' => true]
            ],
            'note_id' => $note->id
        ]);

        $this->assertDatabaseCount('tasks',3);

        $tasks = $note->fresh()->checklist->tasks;

        $this->assertEquals('some task 1', $tasks[0]->text);
        $this->assertEquals('second task', $tasks[1]->text);
        $this->assertEquals('another task', $tasks[2]->text);

        $this->assertTrue($tasks[0]->completed);
        $this->assertFalse($tasks[1]->completed);
        $this->assertTrue($tasks[2]->completed);

        $this->assertEquals(1, $tasks[0]->position);
        $this->assertEquals(2, $tasks[1]->position);
        $this->assertEquals(3, $tasks[2]->position);
    }

    public function test_checklist_and_all_its_tasks_could_be_deleted()
    {
        $owner = User::factory()->create();
        $note = Note::factory()->for($owner, 'owner')->create();
        $checklist = Checklist::factory()->for($note, 'note')->create();
        $tasks = Task::factory()->for($checklist)->count(3)->create();

        auth()->login($owner);

        $this->assertDatabaseCount('checklists', 1);
        $this->assertDatabaseCount('tasks', 3);

        $this->post(route('checklist.destroy', $note->fresh()))->assertJson([
            'header' => $note->header
        ]);

        $this->assertDatabaseCount('checklists', 0);
        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_when_checklist_is_deleted_original_note_content_is_restored()
    {
        $owner = User::factory()->create();
        $note = Note::factory()->for($owner, 'owner')->create();
        $checklist = Checklist::factory()->for($note, 'note')->create();
        $tasks = Task::factory()->for($checklist)->count(3)->create();

        auth()->login($owner);

        $expected = $tasks[0]->text . '<br>' . $tasks[1]->text . '<br>' . $tasks[2]->text . '<br>';
        $this->post(route('checklist.destroy', $note->fresh()));

        $this->assertEquals($expected, $note->fresh()->body);
    }

    public function test_checklist_is_updated_by_rewriting_tasks()
    {
        $owner = User::factory()->create();
        $note = Note::factory()->for($owner, 'owner')->create();
        $checklist = Checklist::factory()->for($note, 'note')->create();
        $tasks = Task::factory()->for($checklist)->count(5)->create();

        auth()->login($owner);

        $this->assertDatabaseCount('tasks', 5);

        $this->put( route('checklist.update', $checklist->id), [
            'tasks' => [
                ['text' => 'some task 1',
                    'completed' => true],
                ['text' => 'second task',
                    'completed' => false],
                ['text' => 'another task',
                    'completed' => true]
            ]
        ] )->assertJsonFragment(['text' => 'some task 1',
            'completed' => true])
           ->assertJsonFragment(['text' => 'second task',
                'completed' => false])
           ->assertJsonFragment(['text' => 'another task',
               'completed' => true]);

        $tasks = $note->fresh()->checklist->tasks;

        $this->assertEquals('some task 1', $tasks[0]->text);
        $this->assertEquals('second task', $tasks[1]->text);
        $this->assertEquals('another task', $tasks[2]->text);

        $this->assertTrue($tasks[0]->completed);
        $this->assertFalse($tasks[1]->completed);
        $this->assertTrue($tasks[2]->completed);

        $this->assertEquals(1, $tasks[0]->position);
        $this->assertEquals(2, $tasks[1]->position);
        $this->assertEquals(3, $tasks[2]->position);
    }

    public function test_all_tasks_could_be_unchecked()
    {
        auth()->login(User::factory()->create());
        $checklist = Checklist::factory()->create();

        Task::factory()->for($checklist)->count(3)->create([ 'completed' => false ]);
        Task::factory()->for($checklist)->count(3)->create([ 'completed' => true  ]);

        $note = $this->post(route('checklist.uncheck_all', $checklist))
                     ->json();

        $this->assertEquals($checklist->id, $note['checklist']['id']);

        $this->assertCount(6, Task::where('completed', false)->get());
    }

    public function test_all_completed_tasks_could_be_removed()
    {
        auth()->login(User::factory()->create());
        $checklist = Checklist::factory()->create();

        Task::factory()->for($checklist)->count(3)->create([ 'completed' => false ]);
        Task::factory()->for($checklist)->count(5)->create([ 'completed' => true  ]);

        $note = $this->post(route('checklist.remove_completed', $checklist))
            ->json();

        $this->assertEquals($checklist->id, $note['checklist']['id']);

        $this->assertDatabaseCount('tasks', 3);
    }
}
