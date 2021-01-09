<?php

use Illuminate\Support\Facades\Route;

function setActiveLink($routeName) {
    return (Route::currentRouteName() == $routeName) ? 'active' : '';
}

function setActiveTagLink($tag) {
    return (Route::currentRouteName() == 'tag') && (Route::input('tag') == $tag) ? 'active' : '';
}
