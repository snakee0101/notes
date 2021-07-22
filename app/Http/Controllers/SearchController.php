<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request) : Collection
    {
        $searchQuery = $request->input('query');
        return Note::search($searchQuery)->get();
    }
}
