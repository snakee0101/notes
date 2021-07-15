<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    use HasFactory;
    public $timestamps = false;

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
}
