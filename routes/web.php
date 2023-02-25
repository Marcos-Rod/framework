<?php

use App\Controllers\HomeController;
use App\Route;

Route::get('/', [HomeController::class, 'index']);

Route::get('/contact', function () {
    return "contacto";
});

Route::get('/services', function () {
    return "servicios";
});

Route::get('/courses/:slug', function($slug){
    return "Bienvenido al curso: $slug";
});

Route::dispatch();