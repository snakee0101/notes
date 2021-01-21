<?php

namespace Tests\Unit;

use App\Models\Tag;
use App\Models\User;
use Tests\TestCase;

class TagTest extends TestCase
{
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
}
