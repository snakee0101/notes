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
}
