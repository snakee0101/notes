<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function destroy(Link $link)
    {
        $link->delete();
    }

    public function restore($link_id)
    {
        Link::onlyTrashed()->find($link_id)->restore();

        return Link::find($link_id);
    }
}
