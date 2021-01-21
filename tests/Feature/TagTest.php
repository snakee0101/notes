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
}
