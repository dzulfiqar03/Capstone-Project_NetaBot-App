<?php

use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('listProduct', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
