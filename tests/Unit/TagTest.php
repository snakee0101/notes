<?php

namespace Tests\Unit;

use App\Models\Note;
use App\Models\Tag;
use App\Models\User;
use Database\Factories\TagFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TagTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    public function test_user_has_many_tags()
    {
        $user = User::factory()->create();
        $tags = TagFactory::times(3)->for($user, 'owner')->create();
        $user->refresh();

        $this->assertInstanceOf(Tag::class, $user->tags()->first());

        $expected_tag_names = $tags->pluck('name')->sort();
        $actual_tag_names = $user->tags()->pluck('name')->sort();

        $this->assertEquals($expected_tag_names, $actual_tag_names);
    }

    public function test_tag_has_an_owner()
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->for($user, 'owner')->create();
        $this->assertInstanceOf(User::class, $tag->owner);
    }

    public function test_user_can_get_only_its_tags()
    {
        $user_1 = User::factory()->create();
        $user_2 = User::factory()->create();

        Tag::factory()->count(5)->for($user_1, 'owner')->create();
        Tag::factory()->count(10)->for($user_2, 'owner')->create();

        auth()->login($user_1);
        $this->assertCount(5, Tag::getAllNames());

        auth()->login($user_2);
        $this->assertCount(10, Tag::getAllNames());
    }

    public function test_user_can_get_notes_for_a_specific_tag()
    {
        $tag = Tag::factory()->has(Note::factory()->count(3))->create();

        $this->assertCount(3, $tag->notes);
        $this->assertInstanceOf(Note::class, $tag->notes->first());
    }

    public function test_tags_should_be_unique_for_specific_user()
    {
        $this->expectExceptionMessage('Integrity constraint violation: 19 UNIQUE constraint failed');

        $user = User::factory()->create();

        $tag_1_data = Tag::factory()->for($user, 'owner')
                               ->has(Note::factory())
                               ->raw();

        Tag::create($tag_1_data);
        $this->assertCount(1, Tag::all());

        Tag::create($tag_1_data);
    }

    public function test_tag_is_removed_from_intermediate_table_when_it_is_deleted()
    {
        $tag = Tag::factory()->create();
        auth()->login($tag->owner);

        $note = Note::factory()->count(3)->hasAttached($tag)->create();
        $this->assertDatabaseCount('note_tag', 3);

        $tag->forceDelete();
        $this->assertDatabaseCount('note_tag', 0);
    }

    public function test_note_is_removed_from_a_pivot_when_it_is_deleted()
    {
        $user = User::factory();
        $tag = Tag::factory()->has($user, 'owner')->count(3);
        auth()->login($user->create());

        $note = Note::factory()->hasAttached($tag)->create();
        $this->assertDatabaseCount('note_tag', 3);

        $note->forceDelete();
        $this->assertDatabaseCount('note_tag', 0);
    }

    public function test_user_tags_cant_be_duplicated()
    {
        $this->expectExceptionMessage('Integrity constraint violation: 19 UNIQUE constraint failed');
        $user = User::factory()->create();

        $user->tags()->create(['name' => 'tag 1']);
        $user->tags()->create(['name' => 'tag 1']);
    }

    public function test_note_tags_cant_be_duplicated()
    {
        $this->expectExceptionMessage('Integrity constraint violation: 19 UNIQUE constraint failed');
        $user = User::factory()->create();
        $tag = $user->tags()->create(['name' => 'tag 1']);

        $note = Note::factory()->create();

        $note->tags()->attach($tag);
        $note->tags()->attach($tag);
    }
}
