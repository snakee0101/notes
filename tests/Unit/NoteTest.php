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
            $mock->shouldReceive('run', 'image');
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

    public function test_a_note_appends_tags_to_json()
    {
        $note = Note::factory()->for(
            User::factory()->create(), 'owner'
        )->hasAttached(Tag::factory()->count(3))
            ->create();


        $json_decoded = json_decode($note->toJson());
        $this->assertObjectHasAttribute('tags', $json_decoded);
        $this->assertCount(3, $json_decoded->tags);
    }

    public function test_a_note_has_images()
    {
        $note = Note::factory()->has(Image::factory()->count(3))->create();
        $this->assertInstanceOf(Image::class, $note->images->first());
    }

    public function test_a_note_could_de_duplicated()
    {
        //Prepare for the test
        $storage = Storage::fake();
        $storage->put('images/123.jpeg', 12345);
        $storage->put('thumbnails_small/456.jpeg', 12345);
        $storage->put('thumbnails_large/789.jpeg', 12345);

        $owner = User::factory()->create();

        $tag = Tag::factory()->create([ 'user_id' => $owner->id ]);
        $note = Note::factory()->create([ 'owner_id' => $owner->id ]);
        $note->tags()->attach($tag);

        $checklist = Checklist::factory()->for($note,'note')
            ->has(Task::factory()->count(3))
            ->create();

        $image = Image::factory()->create([
            'note_id' => $note->id,
            'image_path' => '/storage/images/123.jpeg',
            'thumbnail_small_path' => '/storage/thumbnails_small/456.jpeg',
            'thumbnail_large_path' => '/storage/thumbnails_large/789' . '.jpeg',
        ]);
        $reminder = Reminder::factory()->create([
            'note_id' => $note->id
        ]);

        $note->refresh();

        Storage::assertExists( 'images/123.jpeg');
        Storage::assertExists( 'thumbnails_small/456.jpeg');
        Storage::assertExists( 'thumbnails_large/789.jpeg');

        $this->assertInstanceOf(User::class, $note->owner);
        $this->assertInstanceOf(Tag::class, $note->tags()->first());
        $this->assertInstanceOf(Image::class, $note->images()->first());
        $this->assertInstanceOf(Reminder::class, $note->reminder);

        //Replicate the note
        $replica = $note->makeCopy();

        //Assert that all relationships were duplicated correctly (including files)
        $this->assertDatabaseCount('notes', 2);

        //test note_user intermediate table

        $this->assertDatabaseCount('reminders', 2);
        $this->assertEquals($replica->id, $replica->reminder->note_id);

        $this->assertDatabaseCount('images', 2);

        $this->assertNotEquals($replica->images()->first()->image_path, $note->images()->first()->image_path);
        $this->assertNotEquals($replica->images()->first()->thumbnail_small_path, $note->images()->first()->thumbnail_small_path);
        $this->assertNotEquals($replica->images()->first()->thumbnail_large_path, $note->images()->first()->thumbnail_large_path);

        Storage::assertExists( substr($replica->images()->first()->image_path, 9) );
        Storage::assertExists( substr($replica->images()->first()->thumbnail_small_path, 9) );
        Storage::assertExists( substr($replica->images()->first()->thumbnail_large_path, 9) );

        //files should contain the copy (be of same size)
        $this->assertEquals( Storage::size( substr($note->images()->first()->image_path, 9) ) ,
                             Storage::size( substr($replica->images()->first()->image_path, 9) ) );

        $this->assertEquals( Storage::size( substr($note->images()->first()->thumbnail_small_path, 9) ) ,
                             Storage::size( substr($replica->images()->first()->thumbnail_small_path, 9) ) );

        $this->assertEquals( Storage::size( substr($note->images()->first()->thumbnail_large_path, 9) ) ,
                             Storage::size( substr($replica->images()->first()->thumbnail_large_path, 9) ) );


        $this->assertDatabaseHas('note_tag', [
            'note_id' => $replica->id,
            'tag_id' => $tag->id
        ]);

        //check for replicated checklist
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
        $storage = Storage::fake();
        $storage->put('images/123.jpeg', 12345);
        $storage->put('thumbnails_small/456.jpeg', 12345);
        $storage->put('thumbnails_large/789.jpeg', 12345);

        $owner = User::factory()->create();
        $note = Note::factory()->create([ 'owner_id' => $owner->id ]);

        $image = Image::factory()->create([
            'note_id' => $note->id,
            'image_path' => '/storage/images/123.jpeg',
            'thumbnail_small_path' => '/storage/thumbnails_small/456.jpeg',
            'thumbnail_large_path' => '/storage/thumbnails_large/789' . '.jpeg',
        ]);

        $note->refresh();

        $this->assertDatabaseCount('images', 1);
        $note->forceDelete();
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
        dump(User::firstOrNew(['id' => 1, 'email' => 's@h.h'], ['name' => 'inserted', 'password' => '122']));
        dd(User::all());

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
