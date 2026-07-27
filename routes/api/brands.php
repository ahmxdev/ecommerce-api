<?php

use App\Http\Controllers\Brand\BrandController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('brands', BrandController::class);
    });
});
