<?php

use App\Http\Controllers\Category\CategoryController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('categories', CategoryController::class);
});
