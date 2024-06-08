<?php

namespace Tests\Unit;

use App\Models\Link;
use App\Models\Note;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LinkTest extends TestCase
{
    public function test_a_note_has_many_links()
    {
        $note = Note::factory()->create();
        Link::factory()->count(3)->create(['note_id' => $note->id]);
        $note->refresh();

        $this->assertInstanceOf(Link::class, $note->links()->first());
        $this->assertCount(3, $note->links);
    }

    public function test_a_link_belongs_to_the_note()
    {
        $note = Note::factory()->create();
        $link = Link::factory()->create(['note_id' => $note->id]);

        $this->assertInstanceOf(Note::class, $link->note);
    }

    public function test_note_can_copy_links_relation()
    {
        $note = Note::factory()->create();
        $link = Link::factory()->create(['note_id' => $note->id]);

        $copy = $note->makeCopy();

        $this->assertInstanceOf(Link::class, $copy->links()->first());
        $this->assertDatabaseCount('links',2);

        $link_of_copy = $copy->links()->first();
        $this->assertEquals($link->name, $link_of_copy->name);
        $this->assertEquals($link->url, $link_of_copy->url);
        $this->assertEquals($link->favicon_path, $link_of_copy->favicon_path);
        $this->assertEquals($link->domain, $link_of_copy->domain);
        $this->assertEquals($copy->id, $link_of_copy->note_id);
    }

    public function test_a_the_links_are_automatically_deleted_when_the_note_is_force_deleted()
    {
        $note = Note::factory()->create();
        Link::factory()->count(3)->create(['note_id' => $note->id]);
        $note->refresh();

        $this->assertDatabaseCount('links', 3);

        $note->forceDelete();

        $this->assertDatabaseCount('links', 0);
    }

    public function test_a_specific_note_cant_have_duplicated_urls()
    {
        $this->expectExceptionMessage('Integrity constraint violation: 1062 Duplicate entry');
        $note_1 = Note::factory()->create();

        Link::factory()->for($note_1, 'note')->create(['url' => 'http://www.google.com']);
        Link::factory()->for($note_1, 'note')->create(['url' => 'http://www.google.com']);
    }

    public function test_different_notes_could_have_the_same_urls()
    {
        $note_1 = Note::factory()->create();
        $note_2 = Note::factory()->create();

        Link::factory()->for($note_1, 'note')->create(['url' => 'http://www.google.com']);
        Link::factory()->for($note_2, 'note')->create(['url' => 'http://www.google.com']);

        $this->assertDatabaseCount('links', 2);
    }

    public function test_links_could_be_found_inside_note_body()
    {
        $note = Note::factory()->create([
            'body' => '<div><a href="http://www.google.com">link 1</a><br><br>normal text <a href="http://www.gismeteo.ua">other link</a><br><br>other normal text</div>'
        ]);

        $result = Link::parseNote($note);

        $this->assertIsArray($result);
        $this->assertEquals('link 1', $result[0]['name']);
        $this->assertEquals('other link', $result[1]['name']);

        $this->assertEquals('http://www.google.com', $result[0]['url']);
        $this->assertEquals('http://www.gismeteo.ua', $result[1]['url']);
    }

    public function test_link_domain_could_be_extracted_from_url()
    {
        $result = Link::extractHost('http://www.gismeteo.ua/page/147?param=44');
        $result2 = Link::extractHost('http://gismeteo.ua/page/147?param=44');

        $this->assertEquals('www.gismeteo.ua', $result);
        $this->assertEquals('gismeteo.ua', $result2);
    }

    public function test_link_could_be_persisted_to_DB()
    {
        $note = Note::factory()->create();
        $url = 'https://regexr.com/';
        $name = 'regexr';

        $link = Link::persist($url, $name, $note);

        $this->assertDatabaseCount('links',1);

        $this->assertEquals($name, $link->name);
        $this->assertEquals($url, $link->url);
        $this->assertEquals('https://regexr.com//assets/icons/favicon-32x32.png?1', $link->favicon_path);
        $this->assertEquals('regexr.com', $link->domain);
        $this->assertEquals($note->id, $link->note_id);
    }

    public function test_link_could_be_soft_deleted()
    {
        $note = Note::factory()->create();
        $url = 'https://habr.com/ru/all/';
        $name = 'habr main page';

        $link = Link::persist($url, $name, $note);
        $this->assertDatabaseCount('links',1);

        $link->delete();

        $this->assertSoftDeleted($link);
    }

    public function test_if_old_links_name_is_changed_it_is_persisted_to_DB()
    {
        $note = Note::factory()->create();

        Link::persist('https://habr.com/ru/all/', 'link 1', $note);
        Link::persist('https://regexr.com/', 'other link', $note);

        Link::persist('https://habr.com/ru/all/', 'habr', $note);

        $note->refresh();

        $this->assertDatabaseCount('links', 2);
        $this->assertEquals('habr', $note->links[0]->name);
        $this->assertEquals('https://habr.com/ru/all/', $note->links[0]->url);
    }
}
