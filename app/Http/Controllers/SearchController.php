<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $searchQuery = $request->input('query');
        return Note::search($searchQuery, function(\Meilisearch\Endpoints\Indexes $meilisearch_index, $query, array $options) {
            $meilisearch_index->updateFilterableAttributes(['tags', 'type', 'color']);

            if(request()->has('filterBy') && request()->input('filterBy') === 'color') {
                $options['filter'] = "color = '" . request('filterValue') . "'";
            }

            if(request()->has('filterBy') && request()->input('filterBy') === 'tags') {
                $options['filter'] = "tags = '" . request('filterValue') . "'";
            }

            if(request()->has('filterBy') && request()->input('filterBy') === 'type') {
                $options['filter'] = "type = '" . request('filterValue') . "'";
            }

            return $meilisearch_index->search($query, $options);
        })->paginate(1);
    }
}
