<?php

namespace App\Models;

use App\Utilities\NoteTypeDetector;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Note extends Model
{
    protected $hidden = [];
    protected $perPage = 20;

    use HasFactory, SoftDeletes, Searchable;

    protected $guarded = [];
    protected $casts = [
        'pinned' => 'boolean',
        'archived' => 'boolean',
    ];

    protected $appends = ['reminder'];
    protected $with = ['checklist', 'links', 'images', 'drawings', 'owner', 'collaborators', 'tags'];

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'header' => $this->header,
            'body' => \strip_tags($this->body),
            'color' => $this->color,
            'tags' => $this->tags()->pluck('name')->toArray(),
            'type' => NoteTypeDetector::select($this)->detectTypes(),
            'owners' => [$this->owner_id, ...$this->collaborators->pluck('id')->toArray()],
            'recognized_text' => $this->images->reduce(function($carry, $image) {
                return $carry . ',' . $image->recognized_text;
            })
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

    public function collaborators()
    {
        return $this->belongsToMany(User::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function drawings()
    {
        return $this->hasMany(Drawing::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    public function getReminderAttribute()
    {
        return $this->reminders()->firstWhere('user_id', auth()->id());
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
        $replica = $this->replicate();  //replicate the note
        $replica->push();

        if($this->reminders()->exists()) { //replicate the reminder
            $this->reminders->each(function($reminder) use ($replica) {
                if($replica->owner_id != $reminder->user_id)    //Note replicates only owners reminder
                    return;

                $reminder_replica = $reminder->replicate();
                $reminder_replica->note_id = $replica->id;
                $reminder_replica->push();
            });
        }

        $replica->tags()->saveMany( $this->tags()->get() );   //replicate the tags

        $this?->images->each(function($image) use ($replica) {  //replicate each image and reassign it to new note
            $image->replicate(['id'])
                  ->fill(['note_id' => $replica->id, 'recognized_text' => $image->recognized_text])
                  ->push();
        });

        $this?->drawings->each(function($drawing) use ($replica) {  //replicate each drawing and reassign it to new note
            $drawing->replicate(['id'])
                  ->fill(['note_id' => $replica->id])
                  ->push();
        });

        $replica->collaborators()->saveMany( $this->collaborators ); //replicate the collaborators

        $modified_links = $this->links->map(function($link) {  //replicate the links
            return array_diff_assoc($link->toArray(), ['id' => $link->id]);
        });
        $replica->links()->createMany($modified_links);

        if($this->checklist) { //replicate the checklist
            $checklist = $this->checklist->replicate();
            $checklist->note_id = $replica->id;
            $checklist->push();

            $this->checklist->tasks->each(function($task) use ($checklist) {
                $task->replicate(['id'])
                    ->fill(['checklist_id' => $checklist->id])
                    ->push();
            });
        }

        $replica->push();

        return $replica->fresh();
    }
}
