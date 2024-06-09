<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class SearchController extends Controller
{
 /*function (SearchIndex $algolia, string $query, array $options) {
        $options['body']['query']['bool']['filter']['geo_distance'] = [
            'distance' => '1000km',
            'location' => ['lat' => 36, 'lon' => 111],
        ];

        return $algolia->search($query, $options);
    }
)->get();*/

    public function __invoke(Request $request)
    {
        $searchQuery = $request->input('query');
        return Note::search($searchQuery, function(\Meilisearch\Endpoints\Indexes $meilisearch_index, $query, array $options) {
            $meilisearch_index->updateFilterableAttributes(['tags', 'type', 'color']);

            if(request()->has('filterBy') && request()->input('filterBy') === 'color') {
                $options['filter'] = 'color = ' . request('filterValue');
            }

            if(request()->has('filterBy') && request()->input('filterBy') === 'tag') {
                $options['facetFilters'] = ['tags:' . request('filterValue')];
            }

            if(request()->has('filterBy') && request()->input('filterBy') === 'type') {
                $options['facetFilters'] = ['type:' . request('filterValue')];
            }

            return $meilisearch_index->search($query, $options);
        })->paginate(1);
    }
}
