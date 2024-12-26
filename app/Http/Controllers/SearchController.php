<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        return Note::search($request->input('query'), function(\Meilisearch\Endpoints\Indexes $meilisearch_index, $query, array $options) use ($request) {
            $meilisearch_index->updateFilterableAttributes(['tags', 'type', 'color', 'owners']);

            if($request->filled('filterBy')) {
                $options['filter'] = ["$request->filterBy = '$request->filterValue'"];
            }

            $options['filter'][] = "owners = " . auth()->id();  // seach only notes where you are the owner or collaborator

            return $meilisearch_index->search($query, $options);
        })->get();
    }
}
