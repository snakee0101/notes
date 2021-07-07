<?php

namespace Database\Factories;

use App\Models\Note;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;

class NoteFactory extends Factory
{
    use WithFaker;
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Note::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'header' => $this->faker->sentence,
            'body' => $this->faker->sentence,
            'pinned' => false,
            'archived' => false,
            'owner_id' => User::factory()->create()->id,
            'color' => 'blue',  //TODO: color should be set as the relationship
            'type' => 'text'
        ];
    }

    public function archived()
    {
        return $this->state([
            'archived' => true
        ]);
    }
}
