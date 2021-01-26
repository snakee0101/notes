<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Note extends Model
{
    protected $hidden = [];

    use HasFactory, SoftDeletes;

    protected $appends = ['tags', 'collaborators_json', 'owner_json', 'images_json'];
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

        static::addGlobalScope('hideArchived', function ($query) {
            $query->where('archived', false);
        });

        static::deleting(function (self $object) {
            if (!$object->isForceDeleting())
                return;

            foreach ($object->images as $image) {
                $path_1 = substr($image->image_path, 9);  //offset for '/storage/' part
                $path_2 = substr($image->thumbnail_small_path, 9);
                $path_3 = substr($image->thumbnail_large_path, 9);

                Storage::delete($path_1);
                Storage::delete($path_2);
                Storage::delete($path_3);
            }

            $object->images()->delete();
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

    public function getCollaboratorsJsonAttribute()
    {
        return $this->collaborators()->pluck('email');
    }

    public function getOwnerJsonAttribute()
    {
        return $this->owner;
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function getImagesJsonAttribute()
    {
        return $this->images;
    }
}
