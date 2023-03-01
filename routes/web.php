<?php

use App\Controllers\ContactController;
use App\Controllers\HomeController;
use App\Route;

Route::get('/', [HomeController::class, 'index']);

Route::get('/contacts', [ContactController::class, 'index']);
Route::get('/contact/create', [ContactController::class, 'create']);
Route::post('/contact', [ContactController::class, 'store']);
Route::get('/contact/:id', [ContactController::class, 'show']);
Route::get('/contact/:id/edit', [ContactController::class, 'edit']);
Route::post('/contact/:id', [ContactController::class, 'update']);
Route::post('/contact/:id/delete', [ContactController::class, 'destroy']);




Route::dispatch();