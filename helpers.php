<?php

use Illuminate\Support\Facades\Route;

function setActiveLink($route_name)
{
    return Route::currentRouteName() == $route_name ? 'active' : '';
}
