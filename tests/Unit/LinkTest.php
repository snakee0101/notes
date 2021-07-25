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
        $this->expectExceptionMessage('Integrity constraint violation: 19 UNIQUE constraint failed');
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

    public function test_favicon_could_be_extracted_from_url()
    {
        $http_response_1 = Http::get('https://habr.com/ru/all/')->body();
        $http_response_2 = Http::get('https://laravel.com/docs/8.x/http-client#request-data')->body();
        $http_response_3 = Http::get('https://regexr.com/')->body();


        $this->assertNotFalse( $url_1 = Link::extractFaviconURL($http_response_1, 'https://habr.com/ru/all/') );
        $this->assertNotFalse( $url_2 = Link::extractFaviconURL($http_response_2, 'https://laravel.com/docs/8.x/http-client#request-data') );
        $this->assertNotFalse( $url_3 = Link::extractFaviconURL($http_response_2, 'https://regexr.com/') );

        $this->assertEquals('https://assets.habr.com/habr-web/img/favicons/favicon-32.png', $url_1);
        $this->assertEquals('https://laravel.com/img/favicon/favicon-32x32.png', $url_2);
        $this->assertEquals('https://regexr.com/img/favicon/favicon-32x32.png', $url_3);
    }
}
