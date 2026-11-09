<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategorieController;
use App\Http\Controllers\Api\Order_itemController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register'])
    ->middleware('throttle:6,1');

Route::post('login', [AuthController::class, 'login'])
    ->middleware('throttle:6,1');

Route::post('cart/session', [CartController::class, 'createSession'])
    ->middleware('throttle:10,1');
Route::get('cart', [CartController::class, 'show']);
Route::post('cart/items', [CartController::class, 'addItem']);
Route::put('cart/items/{item}', [CartController::class, 'updateItem'])
    ->whereNumber('item');
Route::delete('cart/items/{item}', [CartController::class, 'removeItem'])
    ->whereNumber('item');
Route::delete('cart/items', [CartController::class, 'clear']);

Route::apiResource('products', ProductController::class)
    ->only(['index', 'show'])
    ->whereNumber('product');

Route::apiResource('categories', CategorieController::class)
    ->only(['index', 'show'])
    ->whereNumber('category');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::put('me', [AuthController::class, 'updateProfile']);
    Route::put('me/password', [AuthController::class, 'changePassword']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::apiResource('orders', OrderController::class)
        ->only(['index', 'show'])
        ->whereNumber('order');

    Route::apiResource('order_items', Order_itemController::class)
        ->only(['index', 'show'])
        ->whereNumber('order_item');

    Route::apiResource('addresses', AddressController::class)
        ->only(['index', 'show', 'store', 'update', 'destroy'])
        ->whereNumber('address');

    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class)
            ->except(['store'])
            ->whereNumber('user');

        Route::apiResource('products', ProductController::class)
            ->only(['store', 'update', 'destroy'])
            ->whereNumber('product');

        Route::apiResource('orders', OrderController::class)
            ->only(['store', 'update', 'destroy'])
            ->whereNumber('order');

        Route::apiResource('order_items', Order_itemController::class)
            ->only(['store', 'update', 'destroy'])
            ->whereNumber('order_item');

        Route::apiResource('categories', CategorieController::class)
            ->only(['store', 'update', 'destroy'])
            ->whereNumber('category');
    });
});
