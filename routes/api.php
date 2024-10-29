<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


//Public routes
Route::post('login', [AuthController::class, 'login']);

//Private routes
Route::middleware(['auth:api', 'log.service.interaction'])->group(function () {
    Route::get('/gifs/search', [AuthController::class, 'searchGifs']);
    Route::get('/gifs/{id}', [AuthController::class, 'getGifById']);
    Route::post('/gifs/favorites', [AuthController::class, 'storeFavoriteGif']);
});