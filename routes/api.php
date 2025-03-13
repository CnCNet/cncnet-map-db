<?php

use App\Http\Controllers\Api\V1\Maps\DownloadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/upload', \App\Http\Controllers\Api\V1\Maps\UploadController::class);
Route::get('/search', \App\Http\Controllers\Api\V1\Maps\SearchController::class)->middleware('throttle:map_search');
Route::get('/{game}/{map}', DownloadController::class);