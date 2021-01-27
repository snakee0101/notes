<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HelpersTest extends TestCase
{
    public function test_setActiveLink()
    {
        $this->get( route('notes') );
        $this->assertEquals('active', setActiveLink('notes') );

        $this->get( route('reminder.index') );
        $this->assertEmpty( setActiveLink('notes') );
    }

    public function test_setActiveTagLink()
    {
        $this->get( route('tag', 'tag 1') );
        $this->assertEquals('active', setActiveTagLink('tag 1') );
        $this->assertEmpty( setActiveTagLink('tag 2') );
    }
}
