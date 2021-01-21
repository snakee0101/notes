<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TagTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase;

    public function test_user_can_create_a_tag()
    {
        $user = User::factory()->create();
        auth()->login($user);

        $this->post( route('tag.store'), ['tag_name' => 'test'] );

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

        $this->delete( route('tag.destroy', $tag->name) );
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

        $this->delete( route('tag.destroy', $tag->name) );

        try {
            $this->delete( route('tag.destroy', $tag_2->name) );
        } finally {
            $this->assertDatabaseCount('tags', 1);
        }
    }
}
