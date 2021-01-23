<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    protected $hidden = [];

    use HasFactory, SoftDeletes;

    protected $appends = ['tags'];
    protected $guarded = [];
    protected $casts = [
        'pinned' => 'boolean',
        'archived' => 'boolean',
    ];

    public function resolveRouteBinding($value, $field = null)
    {
        return static::withArchived()
                     ->withTrashed()
                     ->findOrFail($value);
    }

    protected static function booted()
    {
        parent::boot();
        static::addGlobalScope('hideArchived', function($query){
            $query->where('archived', false);
        });
    }

    public function scopeWithArchived()
    {
        return static::withoutGlobalScope('hideArchived');
    }

    public function scopeOnlyArchived()
    {
        return static::withArchived()->where('archived', true);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function getTagsAttribute()
    {
        return $this->tags()->pluck('name');
    }

    public function collaborators()
    {
        return $this->belongsToMany(User::class);
    }
}
