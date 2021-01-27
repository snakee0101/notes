<?php

namespace Tests\Unit;

use App\Models\Image;
use App\Models\Note;
use App\Models\Reminder;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NoteTest extends TestCase
{
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
    }
}
