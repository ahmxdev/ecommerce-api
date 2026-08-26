<?php

use App\Http\Controllers\Brand\BrandController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::apiResource('brands', BrandController::class);
});
