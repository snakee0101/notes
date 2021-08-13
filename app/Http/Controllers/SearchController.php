<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use MeiliSearch\MeiliSearch;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $searchQuery = $request->input('query');
        return Note::search($searchQuery, function(\MeiliSearch\Endpoints\Indexes $engine, $query, array $options) {

            if(request()->has('filterBy') && request()->input('filterBy') === 'color') {
                $options['filters'] = 'color=' . request('filterValue');
            }

            if(request()->has('filterBy') && request()->input('filterBy') === 'tag') {
                $engine->updateAttributesForFaceting(['tags', 'type']);

                $options['facetFilters'] = ['tags:' . request()->input('filterValue')];
            }

            return $engine->search($query, $options);
        })->paginate();
    }
}
