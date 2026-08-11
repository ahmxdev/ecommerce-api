<?php

use App\Http\Controllers\Cart\CartController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('cart', [CartController::class, 'index']);
    Route::post('cart/items', [CartController::class, 'store']);
    Route::put('cart/items/{cartItem}', [CartController::class, 'update']);
    Route::delete('cart/items/{cartItem}', [CartController::class, 'destroy']);
});
