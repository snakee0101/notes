<?php

namespace Tests\Feature;

use App\Models\Image;
use App\Models\Note;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Mockery\Mock;
use Mockery\MockInterface;
use Tests\TestCase;
use thiagoalessio\TesseractOCR\TesseractOCR;

//test searching by type (+ searching by header and body at the same time)
class SearchTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['scout.driver' => 'meilisearch']);
    }

    public function test_a_note_could_be_searched_by_header()
    {
        $note = Note::factory()->create(['header' => 'test header', 'id' => 100_000_000]);
        $note2 = Note::factory()->create(['header' => 'header 2', 'id' => 100_000_001]);

        auth()->login($note->owner);

        $json = $this->post(route('search'), [
            'query' => 'test'
        ])->content();

        $data = json_decode($json);

        $note->unsearchable();
        $note2->unsearchable();

        $this->assertCount(1, $data);
        $this->assertStringContainsString('test', $data[0]->header);
    }

    public function test_a_note_body_in_search_index_must_have_tags_stripped()
    {
        $note = Note::factory()->create(['body' => "<div>some text in <b>HTML</b><!--tags--></div>", 'id' => 100_000_000]);

        $serialized_note = $note->toSearchableArray();
        $note->unsearchable();

        $this->assertArrayHasKey('body', $serialized_note);
        $this->assertEquals('some text in HTML', $serialized_note['body']);
    }

    public function test_a_note_could_be_searched_by_body()
    {
        $note = Note::factory()->create(['body' => '<div>note <b>body</b></div>', 'id' => 100_000_000]);
        $note2 = Note::factory()->create(['body' => 'found', 'id' => 100_000_001]);

        auth()->login($note->owner);

        $json = $this->post(route('search'), [
            'query' => 'note'
        ])->content();

        $note->unsearchable();
        $note2->unsearchable();

        $data = json_decode($json);

        $this->assertCount(1, $data);
        $this->assertEquals($note->body, $data[0]->body);
    }

    public function test_serialized_note_must_include_its_color()
    {
        $note = Note::factory()->create(['color' => 'orange', 'id' => 100_000_000]);
        $serialized_note = $note->toSearchableArray();

        $note->unsearchable();

        $this->assertArrayHasKey('color', $serialized_note);
        $this->assertEquals('orange', $serialized_note['color']);
    }
    public function test_tags_are_included_in_note_search_index()
    {
        $tags = Tag::factory()->count(3)->create();
        $note = Note::factory()->hasAttached($tags)->create(['id' => 100_000_000]);

        $serialized_note = $note->toSearchableArray();
        $note->unsearchable();

        $this->assertArrayHasKey('tags', $serialized_note);

        $this->assertContains($tags[0]->name, $serialized_note['tags']);
        $this->assertContains($tags[1]->name, $serialized_note['tags']);
        $this->assertContains($tags[2]->name, $serialized_note['tags']);
    }

    public function test_a_note_could_be_filtered_by_color()
    {
        $note = Note::factory()->create(['body' => 'note body', 'color' => 'orange', 'id' => 100_000_000]);
        $note2 = Note::factory()->create(['body' => 'note body', 'color' => 'black', 'owner_id' => $note->owner_id, 'id' => 100_000_001]);
        $note3 = Note::factory()->create(['body' => 'searching for this text', 'color' => 'black', 'owner_id' => $note->owner_id, 'id' => 100_000_002]);
        $note4 = Note::factory()->create(['body' => 'searching for this text', 'color' => 'orange', 'owner_id' => $note->owner_id, 'id' => 100_000_003]);

        auth()->login($note->owner);

        $json = $this->post(route('search'), [
            'query' => 'searching for this text',
            'filterBy' => 'color',
            'filterValue' => 'orange'
        ])->content();

        $note->unsearchable();
        $note2->unsearchable();
        $note3->unsearchable();
        $note4->unsearchable();

        $data = json_decode($json);
        $this->assertCount(1, $data);
        $this->assertEquals('orange', $data[0]->color);
        $this->assertEquals('searching for this text', $data[0]->body);
    }

    public function test_recognized_images_text_is_included_in_search_index()
    {
        $this->forgetMock(TesseractOCR::class);

        $note = Note::factory()->create(['id' => 100_000_000]);
        $image = Image::factory()->forOCR()->create([
            'note_id' => $note->id
        ]);

        $note->refresh();
        $note_recognized_test = $note->toSearchableArray()['recognized_text'];
        $note->unsearchable();

        $this->assertStringContainsString($image->recognized_text, $note_recognized_test);
    }

    public function test_a_note_could_be_filtered_by_tags()
    {
        auth()->login($owner = User::factory()->create());

        $tags = Tag::factory()->for($owner, 'owner')->count(3)->create();

        $note_with_tag_1_and_2 = Note::factory()->for($owner, 'owner')
                                                 ->hasAttached($tags[0])
                                                 ->hasAttached($tags[1])
                                                 ->create(['body' => 'searching for this text', 'id' => 100_000_000]);

        $note_with_tag_2_and_3 = Note::factory()->for($owner, 'owner')
                                                ->hasAttached($tags[1])
                                                ->hasAttached($tags[2])
                                                ->create(['body' => 'searching for this text', 'id' => 100_000_001]);

        $note_with_tag_1_and_2->searchable();
        $note_with_tag_2_and_3->searchable();
        sleep(2);

        $json = $this->postJson(route('search'), [
            'query' => 'searching for this text',
            'filterBy' => 'tags',
            'filterValue' => $tags[0]->name
        ])->content();

        $data = json_decode($json);

        $note_with_tag_1_and_2->unsearchable();
        $note_with_tag_2_and_3->unsearchable();

        $this->assertCount(1, $data);
        $this->assertEquals($note_with_tag_1_and_2->id, $data[0]->id);
    }
}
