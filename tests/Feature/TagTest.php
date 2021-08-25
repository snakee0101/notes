<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\Tag;
use App\Models\User;
use Database\Factories\NoteFactory;
use Database\Factories\TagFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TagTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase, WithFaker;

    public function test_tags_list_could_be_returned_for_specific_note() {
        $user = UserFactory::times(1)->createOne();
        auth()->login($user);

        $note = NoteFactory::times(1)->for($user,'owner')->createOne();
        $tags = TagFactory::times(3)->for($user, 'owner')
            ->create();
        $note->tags()->attach($tags);

        $initial_tags = $note->tags->toArray();

        $res_note = $this->get(route('note.show', $note->id))->json();

        $this->assertContains($initial_tags[0], $res_note['tags']);
        $this->assertContains($initial_tags[1], $res_note['tags']);
        $this->assertContains($initial_tags[2], $res_note['tags']);
    }

    public function test_tags_could_be_toggled()
    {
        $user = UserFactory::times(1)->createOne();
        auth()->login($user);

        $note = NoteFactory::times(1)->for($user,'owner')->createOne();
        $tag = TagFactory::times(1)->for($user, 'owner')
            ->createOne();

        $this->assertNull($note->tags()->first());  //initially there is no tags

        $tag->refresh();
        $note->refresh();

        $r = $this->post( route('tag.toggle', [ //when the tag is toggled first time
            'tag' => $tag->name,
            'note' => $note->id
        ]) );

        $note->refresh();
        $this->assertInstanceOf(Tag::class, $note->tags()->first()); //it should be attached to the note

        $this->post( route('tag.toggle', [  //when the tag is toggled once again
            'tag' => $tag->name,
            'note' => $note->id
        ]) );

        $note->refresh();
        $this->assertNull($note->tags()->first());  //it should be detached
    }

    public function test_tags_list_could_be_returned()
    {
        $user = User::factory()->create();
        auth()->login($user);

        $expected_tags = TagFactory::times(3)->for($user, 'owner')->create();

        $actual_tags = $this->get( route('tag.index') )->json();

        $tag_names = array_map(fn($tag) => $tag['name'], $actual_tags);

        $this->assertContains($expected_tags[0]->name, $tag_names);
        $this->assertContains($expected_tags[1]->name, $tag_names);
        $this->assertContains($expected_tags[2]->name, $tag_names);
    }

    public function test_user_could_get_only_own_tags()
    {
        $user = User::factory()->create();
        $tags = TagFactory::times(3)->for($user, 'owner')->create();

        $user2 = User::factory()->create();
        $tags2 = TagFactory::times(3)->for($user2, 'owner')->create();

        auth()->login($user);

        $actual_tags = $this->get( route('tag.index') )->json();
        $tag_names = array_map(fn($tag) => $tag['name'], $actual_tags);

        $this->assertContains($tags[0]->name, $tag_names );
        $this->assertContains($tags[1]->name, $tag_names );
        $this->assertContains($tags[2]->name, $tag_names );

        $this->assertNotContains($tags2[0]->name, $tag_names );
        $this->assertNotContains($tags2[1]->name, $tag_names );
        $this->assertNotContains($tags2[2]->name, $tag_names );
    }

    public function test_user_can_create_a_tag()
    {
        $user = User::factory()->create();
        auth()->login($user);

        $res_tag = $this->post(route('tag.store'), ['tag_name' => 'test'])->json();
        $this->assertEquals(Tag::first()->name, $res_tag['name']);

        $this->assertCount(1, Tag::all());
        $this->assertEquals('test', Tag::first()->name);
        $this->assertEquals($user->id, Tag::first()->user_id);
    }

    public function test_user_can_delete_a_tag_by_name()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        auth()->login($user);

        $tag = Tag::factory()->for($user, 'owner')
            ->has(Note::factory())
            ->create();
        $this->assertDatabaseCount('tags', 1);

        $this->delete(route('tag.destroy', $tag->name));
        $this->assertDatabaseCount('tags', 0);
    }

    public function test_user_can_delete_only_their_own_tag()
    {
        $user = User::factory()->create();
        $user_2 = User::factory()->create();

        auth()->login($user);

        $tag = Tag::factory()->for($user, 'owner')
            ->has(Note::factory())
            ->create();

        $tag_2 = Tag::factory()->for($user_2, 'owner')
            ->has(Note::factory())
            ->create();

        $this->assertDatabaseCount('tags', 2);

        $this->delete(route('tag.destroy', $tag->name));

        try {
            $this->delete(route('tag.destroy', $tag_2->name));
        } finally {
            $this->assertDatabaseCount('tags', 1);
        }
    }

    public function test_a_tag_could_be_renamed()
    {
        $user = User::factory()->create();
        auth()->login($user);

        $tag = Tag::factory()->for($user, 'owner')
            ->has(Note::factory())
            ->create();

        $this->put(route('tag.update', $tag->name), ['new_name' => 'new tag']);
        $tag->refresh();

        $this->assertEquals('new tag', $tag->name);
    }

    public function test_a_tag_could_be_detached_from_a_note()
    {
        $note = Note::factory()->create();
        auth()->login($note->owner);

        $tag = Tag::factory()->for($note->owner, 'owner')
            ->hasAttached($note)
            ->create();

        $this->assertNotEmpty( $note->fresh()->tags );

        $this->delete( route('detach_tag', [
            'note' => $note,
            'tag' => $tag->name
        ]) );

        $this->assertEmpty( $note->fresh()->tags );
    }

    public function test_a_tag_could_be_added_to_specific_note()
    {
        $note = Note::factory()->create();
        auth()->login($note->owner);

        $tag = Tag::factory()->for($note->owner, 'owner')->create();

        $this->assertEmpty( $note->fresh()->tags );

        $this->post( route('tag.add_to_note', [
            'note' => $note,
            'tag' => $tag->name
        ]) );

        $this->assertNotEmpty( $note->fresh()->tags );
    }
}
