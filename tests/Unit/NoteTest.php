<?php

namespace Tests\Unit;

use App\Models\Checklist;
use App\Models\Image;
use App\Models\Note;
use App\Models\Reminder;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;
use Tests\TestCase;
use thiagoalessio\TesseractOCR\TesseractOCR;

class NoteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(TesseractOCR::class, function (MockInterface $mock) {
            $mock->shouldReceive('run', 'image', 'imageData');
        });
    }

    public function test_a_note_has_an_owner()
    {
        $note = Note::factory()->for(
            User::factory()->create(), 'owner'
        )->create();

        $this->assertInstanceOf(User::class, $note->owner);
    }

    public function test_archived_notes_are_hidden()
    {
        Note::factory()->for(
            User::factory()->create(), 'owner'
        )->archived()->create();

        $this->assertEmpty( Note::all() );

        Note::factory()->for(
            User::factory()->create(), 'owner'
        )->create();

        $this->assertCount(1, Note::all() );
    }

    public function test_archived_notes_could_be_shown()
    {
        Note::factory()->for(
            User::factory()->create(), 'owner'
        )->archived()->create();

        Note::factory()->for(
            User::factory()->create(), 'owner'
        )->create();

        $this->assertCount(2, Note::withArchived()->get() );
    }

    public function test_only_archived_notes_could_be_shown()
    {
        Note::factory()->for(
            User::factory()->create(), 'owner'
        )->archived()->create();

        Note::factory()->for(
            User::factory()->create(), 'owner'
        )->create();

        $this->assertCount(1, Note::onlyArchived()->get() );
    }

    public function test_a_note_has_the_tags()
    {
        $note = Note::factory()->for(
            User::factory()->create(), 'owner'
        )->hasAttached(Tag::factory()->count(3))
         ->create();

        $this->assertInstanceOf(Tag::class, $note->tags()->first());
        $this->assertCount(3, $note->tags);
    }

    public function test_a_note_has_images()
    {
        $note = Note::factory()->has(Image::factory()->count(3))->create();
        $this->assertInstanceOf(Image::class, $note->fresh()->images[0]);
    }

    public function test_a_note_could_de_replicated()
    {
        $owner = User::factory()->create();
        $note = Note::factory()->create([ 'owner_id' => $owner->id ]);
        $note->tags()->attach(
            $tag = Tag::factory()->create([ 'user_id' => $owner->id ])
        );
        $checklist = Checklist::factory()->for($note,'note')
            ->has(Task::factory()->count(3))
            ->create();

        Image::factory()->create([ 'note_id' => $note->id ]);
        Reminder::factory()->create([
            'note_id' => $note->id,
            'user_id' => $note->owner_id
        ]);

        $note->refresh();

        $this->assertInstanceOf(User::class, $note->owner);
        $this->assertInstanceOf(Tag::class, $note->tags()->first());
        $this->assertInstanceOf(Image::class, $note->images()->first());
        $this->assertInstanceOf(Reminder::class, $note->reminders()->first());

        //Replicate the note
        $replica = $note->makeCopy();

        $this->assertDatabaseCount('notes', 2);

        //reminders must be replicated
        $this->assertDatabaseCount('reminders', 2);
        $this->assertEquals($replica->id, $replica->reminders[0]->note_id);

        //images must be replicated
        $this->assertDatabaseCount('images', 2);
        $this->assertEquals($replica->images[0]->image, $note->images[0]->image);
        $this->assertEquals($replica->images[0]->thumbnail, $note->images[0]->thumbnail);

        //tags must be replicated
        $this->assertDatabaseHas('note_tag', [
            'note_id' => $replica->id,
            'tag_id' => $tag->id
        ]);

        //checklist must be replicated
        $this->assertInstanceOf(Checklist::class, $replica->checklist);
        $this->assertInstanceOf(Task::class, $replica->checklist->tasks[0]);

        $this->assertCount(3, $replica->checklist->tasks);
        $this->assertNotEquals($replica->checklist->id, $note->checklist->id);
        $this->assertEquals($replica->checklist->id, $replica->checklist->tasks[0]->checklist_id);
        $this->assertEquals($note->checklist->tasks[0]->text, $replica->checklist->tasks[0]->text);
    }

    public function test_note_duplicates_only_owners_reminder()
    {
        $owner = User::factory()->create();
        $other_user = User::factory()->create();

        $note = Note::factory()->create([ 'owner_id' => $owner->id ]);
        $note->collaborators()->attach($other_user);

        $reminder = Reminder::factory()->for($note,'note')
                                       ->for($note->owner,'owner')
                                       ->create();

        $other_reminder = Reminder::factory()->for($note,'note')
                                       ->for($other_user,'owner')
                                       ->create();

        $this->assertDatabaseCount('reminders', 2);

        auth()->login($owner);
        $duplicated = $note->makeCopy();

        $this->assertDatabaseCount('reminders', 3);
        $this->assertEquals($owner->id, $duplicated->reminder->user_id);
    }

    //note deletion tests

    public function test_image_record_is_deleted_when_the_note_is_deleted()
    {
        $image = Image::factory()->create();
        $this->assertDatabaseCount('images', 1);

        $image->note->forceDelete();
        $this->assertDatabaseCount('images', 0);
    }

    public function test_reminder_is_deleted_when_the_note_is_deleted()
    {
        $owner = User::factory()->create();
        $note = Note::factory()->create([ 'owner_id' => $owner->id ]);
        $reminder = Reminder::factory()->create([ 'note_id' => $note->id ]);

        $note->refresh();

        $this->assertDatabaseCount('reminders', 1);
        $note->forceDelete();
        $this->assertDatabaseCount('reminders', 0);
    }

    public function test_checklist_is_automatically_deleted_when_then_note_is_deleted()
    {
        $note = Note::factory()->create();

        $checklist = Checklist::factory()->for($note, 'note')->create();
        Task::factory()->for($checklist)->create();

        $this->assertDatabaseCount('checklists', 1);
        $note->forceDelete();
        $this->assertDatabaseCount('checklists', 0);
    }

    public function test_tags_are_automatically_detached_when_the_note_is_deleted()
    {
        $note = Note::factory()->for(
            User::factory()->create(), 'owner'
        )->hasAttached(Tag::factory()->count(3))
            ->create();

        $this->assertDatabaseCount('note_tag', 3);
        $note->forceDelete();
        $this->assertDatabaseCount('note_tag', 0);
    }

    public function test_collaborators_are_automatically_detached_when_the_note_is_deleted()
    {
        //dump(User::firstOrNew(['id' => 1, 'email' => 's@h.h'], ['name' => 'inserted', 'password' => '122']));
        //dd(User::all());

        $owner = User::factory()->create();
        $users = User::factory()->count(3);
        $note = Note::factory()->for($owner, 'owner')
            ->hasAttached($users, [], 'collaborators')
            ->create();

        $this->assertDatabaseCount('note_user', 3);
        $note->forceDelete();
        $this->assertDatabaseCount('note_user', 0);
    }
}
