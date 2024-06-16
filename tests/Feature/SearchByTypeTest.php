<?php

namespace Tests\Feature;

use App\Models\Image;
use App\Models\Note;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SearchByTypeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['scout.driver' => 'meilisearch']);
    }

    public function test_when_adding_image_note_can_be_searched_by_type()
    {
        $note = Note::factory()->create(['header' => 'test header', 'id' => 100_000_000]);
        auth()->login($note->owner);

        $this->post( route('image.store'), [
            'image' => UploadedFile::fake()
                ->image('test.jpg', 1000, 1000),
            'note_id' => $note->id
        ])->assertSuccessful();

        $note->refresh();
        $this->assertInstanceOf(Image::class, $note->images[0]);

        sleep(1);
        $json = $this->post(route('search'), [
            'query' => 'test',
            'filterBy' => 'type',
            'filterValue' => 'image'
        ])->content();

        $data = json_decode($json);
        $note->unsearchable();

        $this->assertCount(1, $data);
        $this->assertStringContainsString('test', $data[0]->header);
    }

    public function test_when_removing_image_note_cannot_be_searched_by_type()
    {
        $note = Note::factory()->create(['header' => 'test header', 'id' => 100_000_000]);
        auth()->login($note->owner);

        $this->post( route('image.store'), [
            'image' => UploadedFile::fake()
                ->image('test.jpg', 1000, 1000),
            'note_id' => $note->id
        ])->assertSuccessful();

        $note->refresh();

        $this->post(route('image.destroy', $note->images[0]->id));
        $this->assertSoftDeleted('images', ['id' => $note->images[0]->id]);

        sleep(1);
        $json = $this->post(route('search'), [
            'query' => 'test',
            'filterBy' => 'type',
            'filterValue' => 'image'
        ])->content();

        $data = json_decode($json);
        $note->unsearchable();

        $this->assertCount(0, $data);
    }

    public function test_when_adding_checklist_note_can_be_searched_by_type() //when creating a note, when updating a note
    {

    }

    public function test_when_removing_checklist_note_cannot_be_searched_by_type()
    {

    }

    public function test_when_adding_link_note_can_be_searched_by_type() // when creating a note, when updating a note
    {

    }

    public function test_when_removing_link_note_cannot_be_searched_by_type()
    {

    }
}
