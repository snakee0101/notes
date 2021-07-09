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

    public static function parse(string $input)
    {
        $splitted_text = preg_split("/\<br\>/", $input);

        $stripped_tags = array_map(fn($item) => strip_tags($item), $splitted_text);

        return $stripped_tags;
    }
}
