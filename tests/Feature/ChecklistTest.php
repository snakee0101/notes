<?php

namespace Tests\Feature;

use App\Models\Checklist;
use App\Models\Note;
use App\Models\User;
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
}
