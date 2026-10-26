<?php

use App\Http\Controllers\Api\LinksController;
use Illuminate\Support\Facades\Route;

Route::get('/links/{hash}', [LinksController::class, 'byHash'])->middleware('auth:api');
Route::post('/links', [LinksController::class, 'create'])->middleware('auth:api');
