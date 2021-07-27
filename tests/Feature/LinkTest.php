<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\Note;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use phpDocumentor\Reflection\Types\False_;
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

    public function test_the_link_could_be_soft_deleted()
    {
        $note = Note::factory()->create();
        $url = 'https://habr.com/ru/all/';
        $name = 'habr main page';

        auth()->login($note->owner);

        $link = Link::persist($url, $name, $note);
        $this->assertDatabaseCount('links',1);

        $this->delete( route('link.destroy', $link) );

        $this->assertSoftDeleted($link);
    }

    public function test_the_link_could_be_restored()
    {
        $note = Note::factory()->create();
        $url = 'https://habr.com/ru/all/';
        $name = 'habr main page';

        auth()->login($note->owner);

        $link = Link::persist($url, $name, $note);
        $link->delete();
        $this->assertSoftDeleted($link);

        $this->post( route('link.restore', $link->id) );

        $this->assertDatabaseHas('links', ['deleted_at' => null]);
    }

    public function test_when_the_note_is_updated_new_links_are_added_to_db()
    {
        $note = Note::factory()->create([
            'body' => '<div><a href="https://habr.com/ru/all/">link 1</a><br><br>normal text</div>',
        ]);

        auth()->login($note->owner);

        $note->links()->create([
            'name' => 'link 1',
            'url' => 'https://habr.com/ru/all/',
            'favicon_path' => 'str',
            'domain' => 'habr.com'
        ]);

        $this->put( route('note.update', $note), [
            'header' => 'header',
            'pinned' => false,
            'archived' => false,
            'color' => 'yellow',
            'type' => 'text',
            'body' => '<div><a href="https://habr.com/ru/all/">link 1</a><br><br>normal text <a href="https://regexr.com/">other link</a><br><br>other normal text</div>'
        ]);

        $note->refresh();

        $this->assertDatabaseCount('links', 2);
        $this->assertEquals('https://regexr.com/', $note->links[1]->url);
        $this->assertEquals('other link', $note->links[1]->name);
    }

    public function test_when_the_note_is_updated_links_deleted_from_the_note_body_are_preserved()
    {
        $note = Note::factory()->create([
            'body' => '<div><a href="https://habr.com/ru/all/">link 1</a><br><br>normal text <a href="https://regexr.com/">other link</a><br><br>other normal text</div>',
        ]);

        auth()->login($note->owner);
        Link::persist('https://habr.com/ru/all/', 'link 1', $note);
        Link::persist('https://regexr.com/', 'other link', $note);

        $this->assertDatabaseCount('links', 2);

        $this->put( route('note.update', $note), [
            'header' => 'header',
            'pinned' => false,
            'archived' => false,
            'color' => 'yellow',
            'type' => 'text',
            'body' => '<div><a href="https://habr.com/ru/all/">link 1</a></div>'
        ]);

        $this->assertDatabaseCount('links', 2);
    }

    public function test_if_old_links_name_is_changed_it_is_persisted_to_DB()
    {

    }
}
