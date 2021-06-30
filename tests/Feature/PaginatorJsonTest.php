<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaginatorJsonTest extends TestCase
{

 // /trash
 // /archive
 // /tag
 // /reminder

    public function test_main_page_paginator()
    {
        $owner = User::factory()->create();
        $notes = Note::factory()->for($owner,'owner')
                                ->count(40)
                                ->create();

        auth()->login($owner);

        $http_response = $this->getJson( route('notes') . '?page=1&notes_type=other_notes' );
        $this->assertJson($http_response->content());

        $this->assertStringContainsString("\"header\":\"{$notes->first()->header}\"", $http_response->content());
        $this->assertStringNotContainsString("\"header\":\"{$notes->last()->header}\"", $http_response->content());


        $http_response = $this->getJson( route('notes') . '?page=2&notes_type=other_notes' );
        $this->assertJson($http_response->content());

        $this->assertStringContainsString("\"header\":\"{$notes->last()->header}\"", $http_response->content());
        $this->assertStringNotContainsString("\"header\":\"{$notes->first()->header}\"", $http_response->content());
    }
}
