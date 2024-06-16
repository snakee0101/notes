<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image as InterventionImage;

class Link extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public $timestamps = false;
    protected $touches = ['note'];

    public function resolveRouteBinding($value, $field = null)
    {
        return static::withTrashed()->firstWhere('id', $value);
    }

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

    public static function extractHost($url)
    {
        return parse_url($url, PHP_URL_HOST);
    }

    public static function extractFaviconURL($page_url)
    {
        $scraper = new \Mpclarkson\IconScraper\Scraper();
        $icons = collect($scraper->get($page_url));

        $largest_icon = $icons->filter(function ($icon) {
            return $icon->getType() == "favicon";  //ignore apple icons
        })->map(function ($icon) {    // get the size of all icons, because not every icon has size data
            return new class($icon) {
                public function __construct($icon)
                {
                    $this->href = $icon->getHref();

                    try {   // if icon could not be loaded - assume that it has size of 0
                        $this->size = \Intervention\Image\Facades\Image::make($this->href)->getWidth();
                    } catch(\Exception $e) {
                        $this->size = 0;
                    }
                }
            };
        })->sort(function($icon_1, $icon_2) {  //sort icons by size in descending order
            return $icon_2->size <=> $icon_1->size;
        })->first();

        return $largest_icon->href;
    }

    public static function persist($url, $name, Note $note)
    {
        try {
            $note->links()->create([
                'name' => $name,
                'url' => $url,
                'favicon_path' => self::extractFaviconURL($url),
                'domain' => self::extractHost($url),
            ]);
        } catch (\Exception $e) { //the user needs to update the link's name...
            self::where([
                ['url', $url],
                ['note_id', $note->id]
            ])->update(['name' => $name]); //...when the link with specified name, url, and note_id is not found
        } finally {
            return $note->links()->firstWhere('url', $url);
        }
    }
}
