<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'completed' => 'boolean'
    ];

    public function checklist()
    {
        return $this->belongsTo(Checklist::class);
    }
}
