<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\Note;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LinkTest extends TestCase
{
    public function test_links_are_persisted_in_the_DB_when_the_note_is_created()
    {
        $user = UserFactory::times(1)->createOne();
        auth()->login($user);

        $this->post(route('note.store'), [
            'header' => 'header',
            'body' => '<div><a href="https://habr.com/ru/all/">link 1</a><br><br>normal text <a href="https://regexr.com/">other link</a><br><br>other normal text</div>',
            'pinned' => false,
            'archived' => false,
            'color' => 'blue',
            'type' => 'text'
        ]);

        $this->assertNotNull($note = Note::first());
        $this->assertDatabaseCount('links',2);

        $this->assertCount(2, $note->links);
        $this->assertInstanceOf(Link::class, $note->links[0]);
        $this->assertInstanceOf(Link::class, $note->links[1]);

        $links = $note->links;

        $this->assertEquals('link 1', $links[0]->name);
        $this->assertEquals('other link', $links[1]->name);

        $this->assertEquals('https://habr.com/ru/all/', $links[0]->url);
        $this->assertEquals('https://regexr.com/', $links[1]->url);

        $this->assertEquals('https://assets.habr.com/habr-web/img/favicons/favicon-32.png', $links[0]->favicon_path);
        $this->assertEquals('https://regexr.com/assets/icons/favicon-32x32.png?1', $links[1]->favicon_path);

        $this->assertEquals('habr.com', $links[0]->domain);
        $this->assertEquals('regexr.com', $links[1]->domain);
    }
}
