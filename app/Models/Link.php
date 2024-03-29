<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Link extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public $timestamps = false;

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
        //get the page's html
        $httpResponseBody = Http::get($page_url)->body();

        //clear new lines and spaces
        $new_lines_cleared = preg_replace('/\n/','', $httpResponseBody);
        $spaces_collapsed = preg_replace('/\s+/'," ", $new_lines_cleared);

        //collect all <link> tags
        $all_link_tags = Str::of($spaces_collapsed)->matchAll('/<link[^>]+>/');

        //select only those <link> tags, that contain images
        $all_image_link_tags = $all_link_tags->filter( fn($item) => Str::of($item)->match('/type=\"image/') != '' );
        $links_with_normal_icons = $all_image_link_tags->filter( fn($item) => Str::of($item)->match('/rel=.*[^-]icon"/') != '' );;

        //get the index of largest icon
        $sizes_extracted = $links_with_normal_icons->map( fn($item) => (int)(string)Str::of($item)->match('/sizes="([^"]*)x[^"]*"/') );
        $index_of_item_with_max_icon_size = $sizes_extracted->search( $sizes_extracted->max() );

        //get the largest icon path
        $largest_icon = $links_with_normal_icons->get($index_of_item_with_max_icon_size);
        $largest_icon_path = (string)Str::of($largest_icon)->match('/href="(.+)"/');

        //transform icon path into absolute
        if(Str::of($largest_icon_path)->startsWith('/')) { //path is relative
            return parse_url($page_url, PHP_URL_SCHEME) . "://" . parse_url($page_url, PHP_URL_HOST) . $largest_icon_path;
        } else {
            return $largest_icon_path;
        }
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
