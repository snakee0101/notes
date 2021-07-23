<?php

namespace Database\Factories;

use App\Models\Link;
use App\Models\Note;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\WithFaker;

class LinkFactory extends Factory
{
    use WithFaker;

    protected $model = Link::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'url' => $this->faker->url,
            'favicon_path' => $this->faker->url,
            'domain' => $this->faker->domainName,
            'note_id' => Note::factory()
        ];
    }
}
