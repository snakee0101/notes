<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $searchQuery = $request->input('query');
        return Note::search($searchQuery)->paginate();
    }
}
