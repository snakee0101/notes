<?php

namespace Database\Factories;

use App\Models\Note;
use App\Models\Reminder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReminderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Reminder::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'note_id' => Note::factory(),
            'user_id' => User::factory(),
            'time' => now()->addDay(),
            'repeat' => null
        ];
    }
}
