<?php

namespace Database\Factories;

use App\Models\Checklist;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;

class TaskFactory extends Factory
{
    use WithFaker;
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'checklist_id' => Checklist::factory(),
            'text' => $this->faker->sentence,
            'completed' => random_int(0,1),
            'position' => 1
        ];
    }
}
