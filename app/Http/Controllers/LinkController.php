<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LinkController extends Controller
{
    public function destroy(Link $link)
    {
        if(Gate::denies('link_manipulation', $link->note))
            return response('Only owner and collaborators can manipulate links', 403);

        $link->delete();
    }

    public function restore($link_id)
    {
        Link::onlyTrashed()->find($link_id)->restore();

        return Link::find($link_id);
    }
}
