<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Cart_itemController;
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

    Route::apiResource('carts', CartController::class)
        ->only(['index', 'show'])
        ->whereNumber('cart');

    Route::apiResource('cart_items', Cart_itemController::class)
        ->only(['index', 'show'])
        ->whereNumber('cart_item');

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

        Route::apiResource('carts', CartController::class)
            ->only(['store', 'update', 'destroy'])
            ->whereNumber('cart');

        Route::apiResource('cart_items', Cart_itemController::class)
            ->only(['store', 'update', 'destroy'])
            ->whereNumber('cart_item');

        Route::apiResource('categories', CategorieController::class)
            ->only(['store', 'update', 'destroy'])
            ->whereNumber('category');
    });
});
