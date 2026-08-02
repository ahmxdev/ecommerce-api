<?php

use App\Http\Controllers\Address\AddressController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('addresses', AddressController::class);
});
