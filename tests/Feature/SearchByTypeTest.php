<?php

namespace Tests\Feature;

use App\Models\User;
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

    public function test_when_adding_link_note_can_be_searched_by_type()
    {
        $note_creating = Note::factory()->raw(['header' => 'links added when created', 'body' => '<a href="https://regexr.com/">link 2</a>']); // id will be 100_000_001

        auth()->login(User::factory()->create());

        // when note is created, it is searchable by links
        $this->post(route('note.store'), $note_creating)
        ->assertSuccessful();

        $note_creating = Note::firstWhere('header', 'links added when created');

        sleep(2);
        $json = $this->post(route('search'), [
            'filterBy' => 'type',
            'filterValue' => 'links'
        ])->content();

        $data = json_decode($json);
        $note_creating->unsearchable();

        $this->assertCount(1, $data);
        $this->assertEquals($note_creating->id, $data[0]->id);
    }

    public function test_when_adding_link_to_existing_note_it_can_be_searched_by_type()
    {
        $note_updating = Note::factory()->create(['header' => 'test header', 'id' => 100_000_000]);
        auth()->login($note_updating->owner);

        $this->put(route('note.update', $note_updating), [
            'body' => '<a href="https://regexr.com/">link 2</a>'
        ]);

        sleep(2);
        $json = $this->post(route('search'), [
            'filterBy' => 'type',
            'filterValue' => 'links'
        ])->content();

        $data = json_decode($json);
        $note_updating->unsearchable();

        $this->assertCount(1, $data);
        $this->assertEquals($note_updating->id, $data[0]->id);
    }

    public function test_when_removing_link_note_cannot_be_searched_by_type()
    {
        $note_creating = Note::factory()->raw(['header' => 'links added when created', 'body' => '<a href="https://regexr.com/">link 2</a>']); // id will be 100_000_001

        auth()->login(User::factory()->create());

        // when note is created, it is searchable by links
        $this->post(route('note.store'), $note_creating)
            ->assertSuccessful();

        $note_creating = Note::firstWhere('header', 'links added when created');
        $this->delete( route('link.destroy', $note_creating->links[0]) );

        $this->assertSoftDeleted($note_creating->links[0]);

        sleep(3);
        $json = $this->post(route('search'), [
            'filterBy' => 'type',
            'filterValue' => 'links'
        ])->content();

        $data = json_decode($json);
        $note_creating->unsearchable();

        $this->assertCount(0, $data);
    }
}
