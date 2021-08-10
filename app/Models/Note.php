<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Laravel\Scout\Searchable;

class Note extends Model
{
    protected $hidden = [];
    protected $perPage = 20;

    use HasFactory, SoftDeletes, Searchable;

    protected $appends = ['tags', 'collaborators_json', 'reminder_json'];
    protected $guarded = [];
    protected $casts = [
        'pinned' => 'boolean',
        'archived' => 'boolean',
    ];

    protected $with = ['checklist', 'links', 'images', 'owner'];

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'header' => $this->header,
            'body' => \strip_tags($this->body),
            'color' => $this->color,
            'tags' => implode(',', $this->tags()->pluck('name')->toArray())
        ];
    }

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

            $object->images()->forceDelete();
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

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function reminder()
    {
        return $this->hasOne(Reminder::class);
    }

    public function getReminderJsonAttribute()
    {
        return $this->reminder;
    }

    public function checklist()
    {
        return $this->hasOne(Checklist::class);
    }

    public function links()
    {
        return $this->hasMany(Link::class);
    }

    public function makeCopy()
    {
        //replicate the note
        $replica = $this->replicate();
        $replica->push();

        //replicate the reminder
        if($this->reminder) {
            $reminder = $this->reminder->replicate();
            $reminder->note_id = $replica->id;
            $reminder->push();
        }

        //replicate the tags
            $replica->tags()->saveMany( $this->tags()->get() );
            $replica->push();

        //replicate the image
        if($this->images)
            $this->images->each->makeCopy($replica);

        $replica->push();

        //replicate the collaborators
        $replica->collaborators()->saveMany( $this->collaborators );
        $replica->push();

        //replicate the links
        $modified_links = $this->links->map(function($link) {
            return array_diff_assoc($link->toArray(), ['id' => $link->id]);
        });

        $replica->links()->createMany($modified_links);
        $replica->push();

        return $replica->fresh();
    }
}
