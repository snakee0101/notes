<?php

namespace Tests\Feature;

use App\Models\Note;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class SearchTest extends TestCase
{
    public function test_a_note_could_be_searched_by_header()
    {
        $note = Note::factory()->create(['header' => 'test header']);
        $note2 = Note::factory()->create(['header' => 'header 2']);

        auth()->login($note->owner);

        $json = $this->post(route('search'), [
            'query' => 'test'
        ])->assertOk()->content();

        $data = json_decode($json);

        $this->assertEquals(1, $data->current_page);
        $this->assertCount(1, $data->data);
        $this->assertStringContainsString('test', $data->data[0]->header);
    }

    public function test_a_note_body_in_search_index_must_have_tags_stripped()
    {
        $note = Note::factory()->create(['body' => "<div>some text in <b>HTML</b><!--tags--></div>"]);

        $serialized_note = $note->toSearchableArray();

        $this->assertArrayHasKey('body', $serialized_note);
        $this->assertEquals('some text in HTML', $serialized_note['body']);
    }

    public function test_a_note_could_be_searched_by_body()
    {
        $note = Note::factory()->create(['body' => 'note body']);
        $note2 = Note::factory()->create(['body' => 'found']);

        auth()->login($note->owner);

        $json = $this->post(route('search'), [
            'query' => 'note'
        ])->assertOk()->content();

        $data = json_decode($json);
        $this->assertCount(1, $data->data);
        $this->assertStringContainsString('note', $data->data[0]->body);
    }

    public function test_serialized_note_must_include_its_color()
    {
        $note = Note::factory()->create(['color' => 'orange']);
        $serialized_note = $note->toSearchableArray();

        $this->assertArrayHasKey('color', $serialized_note);
        $this->assertEquals('orange', $serialized_note['color']);
    }
}
