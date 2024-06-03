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


class SearchTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake();
        Storage::makeDirectory('images');
        Storage::makeDirectory('thumbnails_large');
        Storage::makeDirectory('thumbnails_small');

        config(['scout.driver' => 'meilisearch']);
    }

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

    public function test_a_note_could_be_filtered_by_color()
    {
        $note = Note::factory()->create([
            'body' => 'note body',
            'color' => 'orange'
        ]);

        $note2 = Note::factory()->create([
            'body' => 'note body',
            'color' => 'black'
        ]);

        auth()->login($note->owner);

        $json = $this->post(route('search'), [
            'query' => 'note',
            'filterBy' => 'color',
            'filterValue' => 'orange'
        ])->assertOk()->content();

        $data = json_decode($json);
        $this->assertCount(1, $data->data);
        $this->assertEquals('orange', $data->data[0]->color);
    }

    public function test_tags_are_included_in_note_search_index()
    {
        $tags = Tag::factory()->count(3)->create();
        $note = Note::factory()->hasAttached($tags)->create();

        $serialized_note = $note->toSearchableArray();

        $this->assertArrayHasKey('tags', $serialized_note);

        $this->assertContains($tags[0]->name, $serialized_note['tags']);
        $this->assertContains($tags[1]->name, $serialized_note['tags']);
        $this->assertContains($tags[2]->name, $serialized_note['tags']);
    }

    protected function create_3_fake_images(array $texts)
    {
        $paths = [];

        foreach ($texts as $key => $text) {
            $image = imagecreate(200, 200);
            $color = imagecolorallocate($image, 255, 255, 255);
            $text_color = imagecolorallocate($image, 0, 0, 0);
            $font_path = 'storage/app/Roboto-Light.ttf';

            imagefttext($image, 20, 0, 40,40, $text_color, $font_path, $text);
            imagejpeg($image, Storage::path("test_OCR_$key.jpg"));

            $paths[] = Storage::path("test_OCR_$key.jpg");
        }

        return $paths;
    }

    public function test_recognized_images_text_is_included_in_search_index()
    {
        $this->forgetMock(TesseractOCR::class);

        $note = Note::factory()->create();
        $images = [];

        foreach ($this->create_3_fake_images(['OCR test 1', 'new text', 'testing']) as $image_path) {
            $images[] = Image::factory()->count(3)->for($note, 'note')->create([
                'image_path' => $image_path
            ]);
        }

        $note->refresh();

        $this->assertStringContainsString($images[0]->recognized_text, $note->toSearchableArray()['recognized_text']);
        $this->assertStringContainsString($images[1]->recognized_text, $note->toSearchableArray()['recognized_text']);
        $this->assertStringContainsString($images[2]->recognized_text, $note->toSearchableArray()['recognized_text']);
    }

    public function test_a_note_could_be_filtered_by_tags() //TODO: Actually it works but not testable yet
    {
        $owner = User::factory()->create();

        $tags = Tag::factory()->for($owner, 'owner')->count(3)->create();

        $notes_with_tag_1_and_2 = Note::factory()->for($owner, 'owner')
                                                 ->hasAttached($tags[0])
                                                 ->hasAttached($tags[1])
                                                 ->count(3)
                                                 ->create(['body' => 'note']);

        $notes_with_tag_2_and_3 = Note::factory()->for($owner, 'owner')
                                                ->hasAttached($tags[1])
                                                ->hasAttached($tags[2])
                                                ->count(2)
                                                ->create(['body' => 'note']);

        auth()->login($owner);

        $json = $this->post(route('search'), [
            'query' => 'note',
            'filterBy' => 'tag',
            'filterValue' => $tags[0]->name
        ])->assertOk()->content();
        $data = json_decode($json);

        $this->assertCount(3, $data->data);
        $this->assertEquals($notes_with_tag_1_and_2[0]->color, $data->data[0]->color);
    }
}
