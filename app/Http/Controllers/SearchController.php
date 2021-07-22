<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $searchQuery = $request->input('query');
        return Note::search($searchQuery, function($engine, $query, array $options) {

            if(request()->has('filterBy') && request()->input('filterBy') === 'color') {
                $options['filters'] = 'color=' . request('filterValue');
            }

            return $engine->search($query, $options);
        })->paginate();
    }
}
