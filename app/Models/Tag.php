<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = [];

    public function resolveRouteBinding($value, $field = null)
    {
        return static::whereName($value)->first();
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id', 'id', 'owner');
    }

    public static function getAllNames()
    {
        return static::where('user_id', auth()->id())
                     ->pluck('name');
    }

    public function notes()
    {
        return $this->belongsToMany(Note::class, 'note_tag', 'tag_id', 'note_id');
    }
}
