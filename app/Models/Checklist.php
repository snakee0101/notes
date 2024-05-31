<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $touches = ['note'];
    protected $with = ['tasks'];

    public function note()
    {
        return $this->belongsTo(Note::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function uncheckAll()
    {
        $this->tasks()->update(['completed' => false]);
    }

    public function removeCompleted()
    {
        $this->tasks()->where('completed', true)->delete();
    }

    public function toHTML()
    {
        return $this->tasks->reduce(function ($accumulator, $task) {
            return $accumulator . $task->text . '<br>';
        }, '');
    }

    //wraps task text into array and assigns it a position according to its index
    public static function wrap(array $checklist_data)
    {
        return collect($checklist_data)->map(fn($task, $index) => [
            'text' => $task['text'],
            'position' => $index + 1,
            'completed' => $task['completed']
        ]);
    }
}
