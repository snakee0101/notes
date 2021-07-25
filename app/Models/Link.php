<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Link extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function note()
    {
        return $this->belongsTo(Note::class);
    }

    /*
     * Parses note's body to find the links inside.
     * @returns [
     *      ['name' => link_name, 'url' => link_url],
     *      ...
     * ]
     * **/
    public static function parseNote(Note $note) : array
    {
        $urls_with_href = Str::of($note->body)
                             ->matchAll('/<a[\s]+(?=href)([^>]+)>((?:.(?!\<\/a\>))*.)<\/a>/');
        $urls_without_href = $urls_with_href->map(fn($item) => Str::of($item)->matchAll('/href=\"(.*)\"/'))
                                            ->flatten();

        $link_names = Str::of($note->body)
                         ->matchAll('/<a[\s]+(?=href)[^>]+>((?:.(?!\<\/a\>))*.)<\/a>/');

        return $urls_without_href->map(function($url, $index) use ($link_names) {
            return [
                'name' => $link_names[$index],
                'url' => $url,
            ];
        })->toArray();
    }
}
