<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\Order_itemController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\Cart_itemController;
use App\Http\Controllers\Api\AddressController;
use Illuminate\Support\Facades\Route;

Route::apiResource('users', UserController::class)
    ->whereNumber('user');

Route::apiResource('products', ProductController::class)
    ->whereNumber('product');

Route::apiResource('orders', OrderController::class)
    ->whereNumber('order');

Route::apiResource('order_items', Order_itemController::class)
    ->whereNumber('order_item');

Route::apiResource('carts', CartController::class)
    ->whereNumber('cart');

Route::apiResource('cart_items', Cart_itemController::class)
    ->whereNumber('cart_item');

Route::apiResource('addresses', AddressController::class)
    ->whereNumber('address');