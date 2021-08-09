<?php

namespace Tests\Feature;

use App\Models\Checklist;
use App\Models\Link;
use App\Models\Note;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Database\Factories\TagFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RightsTest extends TestCase
{
    private function create_note_with_collaborators()
    {
        $note = Note::factory()->create();
        $collaborator = User::factory()->create();
        $note->collaborators()->attach($collaborator);

        return [$note, $collaborator];
    }

    private function create_note_with_tags()
    {
        $note = Note::factory()->create();
        $tag = Tag::factory()->for($note->owner, 'owner')->create();
        $note->tags()->attach($tag);

        return [$note, $tag];
    }

    /**
     * Note rights
     */
    public function test_note_could_be_duplicated_by_owner_only()
    {
        [$note, $collaborator] = $this->create_note_with_collaborators();

        auth()->login($note->owner); //check for owner's rights
        $this->post( route('note.duplicate', $note) )->assertOk();

        auth()->login($collaborator); //check for collaborator's rights
        $this->post( route('note.duplicate', $note) )->assertForbidden();
    }

    public function test_note_could_be_removed_and_removed_forever_by_owner_only()
    {
        //create the note to test an owner
        $note = Note::factory()->create();

        auth()->login($note->owner);
        $this->delete( route('note.destroy', $note) )->assertOk();
        $this->delete( route('note.destroy', $note) )->assertOk();

        //create new note to test the collaborator
        [$note2, $collaborator] = $this->create_note_with_collaborators();

        auth()->login($collaborator);
        $this->delete( route('note.destroy', $note2) )->assertForbidden();

        auth()->login($note2->owner);
        $this->delete( route('note.destroy', $note2) )->assertOk();
        $this->delete( route('note.destroy', $note2) )->assertOk();
    }

    public function test_note_could_be_restored_by_owner_only()
    {
        //create the note to test an owner
        [$note, $collaborator] = $this->create_note_with_collaborators();
        $note->delete();

        auth()->login($note->owner);
        $this->post( route('note.restore', $note) )->assertOk();

        auth()->login($collaborator);
        $this->post( route('note.restore', $note) )->assertForbidden();
    }

    public function test_note_could_be_returned_to_owner_and_collaborator()
    {
        [$note, $collaborator] = $this->create_note_with_collaborators();

        auth()->login($note->owner);
        $this->get( route('note.show', $note) )->assertOk();

        auth()->login($collaborator);
        $this->get( route('note.show', $note) )->assertOk();

        auth()->login( User::factory()->create() );
        $this->get( route('note.show', $note) )->assertForbidden();
    }

    public function test_notes_archived_state_could_be_updated_by_owner_only()
    {
        $note = Note::factory()->create( ['archived' => false] );
        $collaborator = User::factory()->create();
        $note->collaborators()->attach($collaborator);
        $note->refresh();

        auth()->login($collaborator);
        $this->put( route('note.update', $note), ['archived' => true] )->assertForbidden();

        auth()->login( User::factory()->create() );
        $this->put( route('note.update', $note), ['archived' => true] )->assertForbidden();

        auth()->login($note->owner);
        $this->put( route('note.update', $note), ['archived' => true] )->assertOk();
    }

    public function test_other_note_properties_could_be_updated_by_owner_and_collaborators()
    {
        [$note, $collaborator] = $this->create_note_with_collaborators();

        auth()->login($collaborator);
        $this->put( route('note.update', $note), ['header' => 'new header'] )->assertOK();

        auth()->login( User::factory()->create() );
        $this->put( route('note.update', $note), ['header' => 'new header 2'] )->assertForbidden();

        auth()->login($note->owner);
        $this->put( route('note.update', $note), ['header' => 'new header 3'] )->assertOk();
    }

    /**
     * Collaborator rights
     */
    public function test_only_note_owner_can_synchronize_the_collaborators()
    {
        $note = Note::factory()->create();
        $collaborator = User::factory()->create();
        $collaborator2 = User::factory()->create();
        $note->collaborators()->attach($collaborator);
        $note->refresh();

        auth()->login($collaborator);
        $this->post( route('sync_collaborator', $note), ['emails' => [$collaborator2->email]] )->assertForbidden();

        auth()->login($note->owner);
        $this->post( route('sync_collaborator', $note), ['emails' => [$collaborator2->email]] )->assertOk();
    }

    /**
     * Image rights
     */
    public function generate_image() : File
    {
        return UploadedFile::fake()
            ->image('test.jpg', 1000, 1000);
    }

    public function test_an_image_could_be_uploaded_by_owner_and_collaborators()
    {
        Storage::fake();
        Storage::makeDirectory('thumbnails_large');
        Storage::makeDirectory('thumbnails_small');

        [$note, $collaborator] = $this->create_note_with_collaborators();

        auth()->login($note->owner);
        $this->post(route('image.store'), [
            'image' => $this->generate_image(),
            'note_id' => $note->id
        ])->assertSuccessful();

        auth()->login($collaborator);
        $this->post(route('image.store'), [
            'image' => $this->generate_image(),
            'note_id' => $note->id
        ])->assertSuccessful();

        auth()->login( $user2 = User::factory()->create() );
        $this->post(route('image.store'), [
            'image' => $this->generate_image(),
            'note_id' => $note->id
        ])->assertForbidden();
    }

    public function test_an_image_could_be_destroyed_by_owner_and_collaborators()
    {
        $storage = Storage::fake();
        Storage::makeDirectory('thumbnails_large');
        Storage::makeDirectory('thumbnails_small');

        [$note, $collaborator] = $this->create_note_with_collaborators();

        $storage->put('images/123.jpeg', '12345');
        $storage->put('thumbnails_small/456.jpeg', '12345');
        $storage->put('thumbnails_large/789.jpeg', '12345');

        $note->images()->create([
            'note_id' => $note->id,
            'image_path' => '/storage/images/123.jpeg',
            'thumbnail_small_path' => '/storage/thumbnails_small/456.jpeg',
            'thumbnail_large_path' => '/storage/thumbnails_large/789.jpeg',
        ]);

        $note->images()->create([
            'note_id' => $note->id,
            'image_path' => '/storage/images/1234.jpeg',
            'thumbnail_small_path' => '/storage/thumbnails_small/4567.jpeg',
            'thumbnail_large_path' => '/storage/thumbnails_large/78910.jpeg',
        ]);

        $note->images()->create([
            'note_id' => $note->id,
            'image_path' => '/storage/images/12345.jpeg',
            'thumbnail_small_path' => '/storage/thumbnails_small/45678.jpeg',
            'thumbnail_large_path' => '/storage/thumbnails_large/7891011.jpeg',
        ]);

        $note->refresh();

        auth()->login($note->owner);
        $this->post( route('image.destroy', $note->images[0]))->assertOk();

        auth()->login($collaborator);
        $this->post( route('image.destroy', $note->images[1]))->assertOk();

        auth()->login( User::factory()->create() );
        $this->post( route('image.destroy', $note->images[2]))->assertForbidden();
    }

    public function test_an_image_could_be_restored_by_owner_and_collaborators()
    {
        $storage = Storage::fake();
        Storage::makeDirectory('thumbnails_large');
        Storage::makeDirectory('thumbnails_small');

        [$note, $collaborator] = $this->create_note_with_collaborators();

        $storage->put('images/123.jpeg', '12345');
        $storage->put('thumbnails_small/456.jpeg', '12345');
        $storage->put('thumbnails_large/789.jpeg', '12345');

        $image_1 = $note->images()->create([
            'note_id' => $note->id,
            'image_path' => '/storage/images/123.jpeg',
            'thumbnail_small_path' => '/storage/thumbnails_small/456.jpeg',
            'thumbnail_large_path' => '/storage/thumbnails_large/789.jpeg',
        ]);

        $image_2 = $note->images()->create([
            'note_id' => $note->id,
            'image_path' => '/storage/images/1234.jpeg',
            'thumbnail_small_path' => '/storage/thumbnails_small/4567.jpeg',
            'thumbnail_large_path' => '/storage/thumbnails_large/78910.jpeg',
        ]);

        $image_3 = $note->images()->create([
            'note_id' => $note->id,
            'image_path' => '/storage/images/12345.jpeg',
            'thumbnail_small_path' => '/storage/thumbnails_small/45678.jpeg',
            'thumbnail_large_path' => '/storage/thumbnails_large/7891011.jpeg',
        ]);
        $note->refresh();
        $image_1->delete();
        $image_2->delete();
        $image_3->delete();

        $this->assertSoftDeleted('images', ['id' => $image_1->id]);
        $this->assertSoftDeleted('images', ['id' => $image_2->id]);
        $this->assertSoftDeleted('images', ['id' => $image_2->id]);


        auth()->login($note->owner);
        $this->put("/image/restore/$image_1->id")->assertOk();

        auth()->login($collaborator);
        $this->put("/image/restore/$image_2->id")->assertOk();

        auth()->login( User::factory()->create() );
        $this->put("/image/restore/$image_3->id")->assertForbidden();
    }

    public function test_an_image_could_be_recognized_by_owner_and_collaborators()
    {
        $storage = Storage::fake();
        $image = imagecreate(200, 200);
        imagejpeg($image, Storage::path('test_OCR.jpg'));

        [$note, $collaborator] = $this->create_note_with_collaborators();

        $note->images()->create([
            'image_path' => Storage::path('test_OCR.jpg'),
            'thumbnail_large_path' => Storage::path('test_OCR.jpg'),
            'thumbnail_small_path' => Storage::path('test_OCR.jpg'),
        ]);
        $note->refresh();

        auth()->login($note->owner);
        $status = $this->post(route('image.recognize'), [
            'image_path' => Storage::path('test_OCR.jpg')
        ])->status();
        $this->assertNotEquals(403, $status);

        auth()->login($collaborator);
        $status = $this->post(route('image.recognize'), [
            'image_path' => Storage::path('test_OCR.jpg')
        ])->status();
        $this->assertNotEquals(403, $status);

        auth()->login( User::factory()->create() );
        $this->post(route('image.recognize'), [
            'image_path' => Storage::path('test_OCR.jpg')
        ])->assertForbidden();

        imagedestroy($image);
    }

    /**
     * Link rights
     */
    public function test_link_could_be_deleted_by_owner_and_collaborators()
    {
        [$note, $collaborator] = $this->create_note_with_collaborators();

        $link_1 = $note->links()->create(['name' => 'link 1', 'url' => 'https://habr.com/ru/all/', 'favicon_path' => 'test', 'domain' => 'test']);
        $link_2 = $note->links()->create(['name' => 'other link', 'url' => 'https://regexr.com/', 'favicon_path' => 'test', 'domain' => 'test']);
        $link_3 = $note->links()->create(['name' => 'google', 'url' => 'https://www.google.com/', 'favicon_path' => 'test', 'domain' => 'test']);

        $note->refresh();

        auth()->login( $note->owner );
        $this->delete( route('link.destroy', $link_1) );
        $this->assertSoftDeleted($link_1);

        auth()->login( $collaborator );
        $this->delete( route('link.destroy', $link_2) );
        $this->assertSoftDeleted($link_2);

        auth()->login( User::factory()->create() );
        $this->delete( route('link.destroy', $link_3) );
        $this->assertNull( $link_3->fresh()->deleted_at );

    }

    public function test_the_link_could_be_restored_by_owner_and_collaborators()
    {
        [$note, $collaborator] = $this->create_note_with_collaborators();

        $note->refresh();
        auth()->login($note->owner);

        $link = Link::persist('https://habr.com/ru/all/', 'habr main page', $note);
        $link_2 = Link::persist('https://www.google.com', 'second link', $note);
        $link_3 = Link::persist('https://www.yandex.ru', 'third link', $note);

        $link->delete();
        $link_2->delete();
        $link_3->delete();

        $this->assertSoftDeleted($link);
        $this->assertSoftDeleted($link_2);
        $this->assertSoftDeleted($link_3);

        auth()->login($note->owner);
        $this->post( route('link.restore', $link->id) );
        $this->assertDatabaseHas('links', ['id' => $link->id, 'deleted_at' => null]);

        auth()->login($collaborator);
        $this->post( route('link.restore', $link_2->id) );
        $this->assertDatabaseHas('links', ['id' => $link_2->id, 'deleted_at' => null]);

        auth()->login( User::factory()->create() );
        $this->post( route('link.restore', $link_3->id) );
        $this->assertDatabaseMissing('links', ['id' => $link_3->id, 'deleted_at' => null]);
    }

    /**
     * Checklist tests
     */
    public function test_checklist_could_be_created_by_note_owner_or_collaborator()
    {
        $note = Note::factory()->create();

        [$note2, $collaborator] = $this->create_note_with_collaborators();

        auth()->login($note->owner);
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
        ])->assertOk();
        $this->assertDatabaseCount('tasks',3);

        auth()->login($collaborator);
        $this->post( route('checklist.store'), [
            'checklist_data' => [
                ['text' => 'task 3',
                    'completed' => true],
                ['text' => 'task 4',
                    'completed' => false],
                ['text' => 'task 5',
                    'completed' => true]
            ],
            'note_id' => $note2->id
        ])->assertOk();
        $this->assertDatabaseCount('tasks',6);

        auth()->login( User::factory()->create() );
        $this->post( route('checklist.store'), [
            'checklist_data' => [
                ['text' => 'task 6',
                    'completed' => true],
                ['text' => 'task 7',
                    'completed' => false],
                ['text' => 'task 8',
                    'completed' => true]
            ],
            'note_id' => $note2->id
        ])->assertForbidden();
        $this->assertDatabaseCount('tasks',6);
    }

    public function test_tasks_could_be_unchecked_by_owner_and_collaborator()
    {
        [$note, $collaborator] = $this->create_note_with_collaborators();

        $checklist = Checklist::factory()->for($note, 'note')->create();
        $tasks = Task::factory()->for($checklist)->count(3)->create( ['completed' => true] );
        $note->refresh();

        $this->assertCount(3, Task::where('completed', true)->get() );

        auth()->login($note->owner);
        $this->post(route('checklist.uncheck_all', $checklist))->assertOk();
        $this->assertCount(0, Task::where('completed', true)->get() );

        Task::where('completed', false)->update( ['completed' => true] );
        $this->assertCount(3, Task::where('completed', true)->get() );


        auth()->login($collaborator);
        $this->post(route('checklist.uncheck_all', $checklist))->assertOk();
        $this->assertCount(0, Task::where('completed', true)->get() );

        Task::where('completed', false)->update( ['completed' => true] );
        $this->assertCount(3, Task::where('completed', true)->get() );


        auth()->login( User::factory()->create() );
        $this->post(route('checklist.uncheck_all', $checklist))->assertForbidden();
        $this->assertCount(3, Task::where('completed', true)->get() );
    }

    public function test_completed_tasks_could_be_removed_by_owner_and_collaborator()
    {
        [$note, $collaborator] = $this->create_note_with_collaborators();

        $checklist = Checklist::factory()->for($note, 'note')->create();
        Task::factory()->for($checklist)->count(3)->create([ 'completed' => false ]);
        Task::factory()->for($checklist)->count(5)->create([ 'completed' => true  ]);

        $note->refresh();

        auth()->login($note->owner);
        $this->post(route('checklist.remove_completed', $checklist))
                     ->assertOk();
        $this->assertDatabaseCount('tasks', 3);

        Task::factory()->for($checklist)->count(5)->create([ 'completed' => true  ]);
        $this->assertDatabaseCount('tasks', 8);

        auth()->login($collaborator);
        $this->post(route('checklist.remove_completed', $checklist))
            ->assertOk();
        $this->assertDatabaseCount('tasks', 3);

        Task::factory()->for($checklist)->count(5)->create([ 'completed' => true  ]);
        $this->assertDatabaseCount('tasks', 8);

        auth()->login( User::factory()->create() );
        $this->post(route('checklist.remove_completed', $checklist))
            ->assertForbidden();
        $this->assertDatabaseCount('tasks', 8);
    }

    public function test_checklist_could_be_removed_by_owner_and_collaborator()
    {
        [$note, $collaborator] = $this->create_note_with_collaborators();

        Checklist::factory()->for($note, 'note')->has( Task::factory()->count(3) )->create();
        $note->refresh();

        auth()->login($note->owner);
        $this->assertInstanceOf(Checklist::class, $note->checklist);
        $this->post( route('checklist.destroy', $note) );
        $this->assertNull($note->fresh()->checklist);

        Checklist::factory()->for($note, 'note')->has( Task::factory()->count(3) )->create();
        $note->refresh();

        auth()->login($collaborator);
        $this->assertInstanceOf(Checklist::class, $note->checklist);
        $this->post( route('checklist.destroy', $note) );
        $this->assertNull($note->fresh()->checklist);

        Checklist::factory()->for($note, 'note')->has( Task::factory()->count(3) )->create();
        $note->refresh();

        auth()->login( User::factory()->create() );
        $this->assertInstanceOf(Checklist::class, $note->checklist);
        $this->post( route('checklist.destroy', $note) );
        $this->assertInstanceOf(Checklist::class, $note->fresh()->checklist);
    }

    public function test_checklist_could_be_updated_by_owner_or_collaborator()
    {
        [$note, $collaborator] = $this->create_note_with_collaborators();

        Checklist::factory()->for($note, 'note')->has( Task::factory()->count(5) )->create();
        $note->refresh();

        $this->assertDatabaseCount('tasks', 5);

        auth()->login($note->owner);
        $this->put( route('checklist.update', $note->checklist->id), [
            'tasks' => [ ['text' => 'some task 1', 'completed' => true],
                ['text' => 'second task', 'completed' => false],
                ['text' => 'another task', 'completed' => true] ]
        ] )->assertOk();
        $this->assertDatabaseCount('tasks', 3);

        $note->checklist()->delete();
        Checklist::factory()->for($note, 'note')->has( Task::factory()->count(5) )->create();
        $this->assertDatabaseCount('tasks', 5);
        $note->refresh();

        auth()->login($collaborator);
        $this->put( route('checklist.update', $note->checklist->id), [
            'tasks' => [ ['text' => 'task 4', 'completed' => true],
                ['text' => 'task 5', 'completed' => false],
                ['text' => 'task 6', 'completed' => true] ]
        ] )->assertOk();

        $this->assertDatabaseCount('tasks', 3);

        Checklist::factory()->for($note, 'note')->has( Task::factory()->count(2) )->create();
        $this->assertDatabaseCount('tasks', 5);

        auth()->login( User::factory()->create() );
        $this->put( route('checklist.update', $note->checklist->id), [
            'tasks' => [ ['text' => 'task 4', 'completed' => true],
                ['text' => 'task 5', 'completed' => false],
                ['text' => 'task 6', 'completed' => true] ]
        ] )->assertForbidden();
        $this->assertDatabaseCount('tasks', 5);
    }

    /**
     * Tag rights
     */
    public function test_only_owner_can_see_the_notes_associated_with_the_tag()
    {
        [$note, $tag] = $this->create_note_with_tags();
        [$note2, $tag2] = $this->create_note_with_tags();

        auth()->login($note->owner);
        $this->get( route('tag.show', $tag->name) )->assertOk();
        $this->get( route('tag.show', $tag2->name) )->assertNotFound();

        auth()->login($note2->owner);
        $this->get( route('tag.show', $tag->name) )->assertNotFound();
        $this->get( route('tag.show', $tag2->name) )->assertOk();
    }

    public function test_only_owner_can_destroy_tags()
    {
        [$note, $tag] = $this->create_note_with_tags();
        [$note2, $tag2] = $this->create_note_with_tags();

        $note->refresh();
        $note2->refresh();

        auth()->login($note->owner);
        $this->delete( route('tag.destroy', $tag->name) )->assertOk();
        $this->delete( route('tag.destroy', $tag2->name) )->assertNotFound();

        auth()->login($note2->owner);
        $this->delete( route('tag.destroy', $tag->name) )->assertNotFound();
        $this->delete( route('tag.destroy', $tag2->name) )->assertOk();
    }

    public function test_only_owner_can_update_tags_name()
    {
        $owner = User::factory()->create();
        $note = Note::factory()->for($owner, 'owner')->create();
        $tag = Tag::factory()->for($owner, 'owner')->create(['name' => 'tag 1']);
        $note->tags()->attach($tag);

        $owner2 = User::factory()->create();
        $note2 = Note::factory()->for($owner2, 'owner')->create();
        $tag2 = Tag::factory()->for($owner2, 'owner')->create(['name' => 'tag 2']);
        $note2->tags()->attach($tag2);

        $note->refresh();
        $note2->refresh();

        auth()->login($owner);
        $this->put( route('tag.update', $tag->name), ['new_name' => 'tag 1 updated'] )->assertOk();
        $this->put( route('tag.update', $tag2->name), ['new_name' => 'tag 2 updated'] )->assertNotFound();

        auth()->login($owner2);
        $this->put( route('tag.update', $tag->name), ['new_name' => 'tag 1 updated'] )->assertNotFound();
        $this->put( route('tag.update', $tag2->name), ['new_name' => 'tag 2 updated'] )->assertOk();
    }

    public function test_only_owner_can_toggle_the_tags()
    {
        $owner = User::factory()->create();
        $note = Note::factory()->for($owner, 'owner')->create();
        $tag = Tag::factory()->for($owner, 'owner')->create(['name' => 'tag 1']);

        $collaborator = User::factory()->create();
        $note->collaborators()->attach($collaborator);

        $note->refresh();

        $this->assertNull($note->tags()->first());

        auth()->login($owner);
        $this->post( route('tag.toggle', [
            'tag' => $tag->name,
            'note' => $note->id
        ]) )->assertOk();

        $note->refresh();
        $this->assertInstanceOf(Tag::class, $note->tags()->first());


        auth()->login($collaborator);
        $this->post( route('tag.toggle', [
            'tag' => $tag->name,
            'note' => $note->id
        ]) );

        $note->refresh();
        $this->assertInstanceOf(Tag::class, $note->tags()->first());
    }

    public function test_a_tag_could_be_added_to_specific_note_by_owner_only()
    {
        [$note, $collaborator] = $this->create_note_with_collaborators();

        auth()->login($note->owner); //check owner rights
        $tag = Tag::factory()->for($note->owner, 'owner')->create();
        $this->assertEmpty( $note->fresh()->tags );

        $this->post( route('tag.add_to_note', [
            'note' => $note,
            'tag' => $tag->name
        ]) )->assertOk();
        $this->assertNotEmpty( $note->fresh()->tags );

        $note->tags()->detach($tag);
        $note->refresh();

        auth()->login($collaborator); //check collaborator rights
        $this->assertEmpty( $note->fresh()->tags );

        $this->post( route('tag.add_to_note', [
            'note' => $note,
            'tag' => $tag->name
        ]) )->assertNotFound();
        $this->assertEmpty( $note->fresh()->tags );
    }

    public function test_a_tag_could_be_removed_from_specific_note_by_owner_only()
    {
        [$note, $collaborator] = $this->create_note_with_collaborators();

        $tag = Tag::factory()->for($note->owner, 'owner')
            ->hasAttached($note)
            ->create();

        $note->refresh();
        $this->assertNotEmpty( $note->fresh()->tags );

        auth()->login($collaborator);
        $this->delete( route('tag.remove_from_note', [
            'note' => $note,
            'tag' => $tag->name
        ]) );
        $this->assertNotEmpty( $note->fresh()->tags );

        auth()->login($note->owner);
        $this->delete( route('tag.remove_from_note', [
            'note' => $note,
            'tag' => $tag->name
        ]) );
        $this->assertEmpty( $note->fresh()->tags );
    }
}
