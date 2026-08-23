<?php

use App\Http\Controllers\Order\OrderController;
use App\Models\Order;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/{order}', [OrderController::class, 'show']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus']);
});
