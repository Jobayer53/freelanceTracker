<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Client_Controller;

Route::get('/', function () {
    return view('welcome');
});

