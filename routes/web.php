<?php

use App\Http\Controllers\LinksController;
use Illuminate\Support\Facades\Route;

Route::get('/{hash}', [LinksController::class, 'process']);
