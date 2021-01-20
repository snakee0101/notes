<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $hidden = [];

    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'pinned' => 'boolean',
        'archived' => 'boolean',
    ];

    public function owner() {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
