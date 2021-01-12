<?php

use Illuminate\Support\Facades\Route;

function getActiveTag() {
    return Route::input('tag');
}
