<?php

use Illuminate\Support\Facades\Route;

function setActiveTagLink($tag) {
    return (Route::currentRouteName() == 'tag') && (Route::input('tag') == $tag) ? 'active' : '';
}
