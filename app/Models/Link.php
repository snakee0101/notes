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

    public static function extractHost($url)
    {
        return parse_url($url, PHP_URL_HOST);
    }

    public static function extractFaviconURL(string $httpResponseBody, $url)
    {
        $new_lines_cleared = preg_replace('/\n/','', $httpResponseBody);
        $spaces_collapsed = preg_replace('/\s+/'," ", $new_lines_cleared);

        $all_link_tags = Str::of($spaces_collapsed)->matchAll('/<link[^>]+>/');

        $all_image_link_tags = $all_link_tags->filter( fn($item) => Str::of($item)->match('/type=\"image/') != '' );
        $links_with_normal_icons = $all_image_link_tags->filter( fn($item) => Str::of($item)->match('/rel=.*[^-]icon"/') != '' );;

        dd($links_with_normal_icons);
        //get icon with max size

        $last_icon = $links_with_normal_icons->last();
        $matched_path = (string)Str::of($last_icon)->match('/href="(.+)"/');

        if(Str::of($matched_path)->startsWith('/')) { //path is relative
            return parse_url($url, PHP_URL_SCHEME) . "://" . parse_url($url, PHP_URL_HOST) . $matched_path;
        } else {
            return $matched_path;
        }
    }
}
