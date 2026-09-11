<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::post('/customers', [CustomerController::class, 'store']);

Route::post('/products', [ProductController::class, 'store']);

Route::post('/orders', [OrderController::class, 'store']);

Route::get('/customers/{email}/orders', [CustomerController::class, 'orders']);

Route::get('/products/low-stock', [ProductController::class, 'lowStock']);