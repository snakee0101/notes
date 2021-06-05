<?php

namespace Tests\Unit;

use App\Models\Note;
use Database\Factories\NoteFactory;
use Database\Factories\UserFactory;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_user_has_notes()
    {
        $user = UserFactory::times(1)->createOne();
        NoteFactory::times(2)->for($user, 'owner')->create();
        $user->refresh();

        $this->assertEquals(2, $user->notes()->count());
        $this->assertInstanceOf(Note::class, $user->notes()->first());
    }
}
