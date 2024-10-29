<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GifController;

//Public routes
Route::post('login', [AuthController::class, 'login']);

//Private routes
Route::middleware(['auth:api', 'log.service.interaction'])->group(function () {
    Route::get('/gifs/search', [GifController::class, 'searchGifs']);
    Route::get('/gifs/{id}', [GifController::class, 'getGifById']);
    Route::post('/gifs/favorites', [GifController::class, 'storeFavoriteGif']);
});