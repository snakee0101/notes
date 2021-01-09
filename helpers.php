<?php

use Illuminate\Support\Facades\Route;

function setActiveLink($routeName) {
    return (Route::currentRouteName() == $routeName) ? 'active' : '';
}
