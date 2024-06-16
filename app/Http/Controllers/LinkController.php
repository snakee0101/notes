<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Support\Facades\Gate;

class LinkController extends Controller
{
    public function destroy(Link $link)
    {
        abort_if(Gate::denies('link_manipulation', $link->note), 403, 'Only owner and collaborators can manipulate links');

        $note = $link->note;
        $link->delete();
        $note->searchable();
    }

    public function restore(Link $link)
    {
        abort_if(Gate::denies('link_manipulation', $link->note), 403, 'Only owner and collaborators can manipulate links');

        $link->restore();
        $link->note->searchable();

        return $link->fresh();
    }
}
