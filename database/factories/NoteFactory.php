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
        $colors = ['white', 'red', 'orange', 'yellow',
            'green', 'teal', 'blue', 'dark-blue',
            'purple', 'pink', 'brown', 'grey'];

        return [
            'header' => $this->faker->sentence,
            'body' => $this->faker->sentence,
            'pinned' => random_int(0,1),
            'archived' => false,
            'owner_id' => User::factory(),
            'color' => $colors[array_rand($colors)],
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
