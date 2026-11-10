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

// =rotas publicas=
//autenticação basica
Route::post('register', [AuthController::class, 'register'])
    ->middleware('throttle:6,1');

Route::post('login', [AuthController::class, 'login'])
    ->middleware('throttle:6,1');

//carrinho de compras
Route::post('cart/session', [CartController::class, 'createSession'])
    ->middleware('throttle:10,1');

Route::get('cart', [CartController::class, 'show']);

Route::post('cart/items', [CartController::class, 'addItem']);

Route::put('cart/items/{item}', [CartController::class, 'updateItem'])
    ->whereNumber('item');

Route::delete('cart/items/{item}', [CartController::class, 'removeItem'])
    ->whereNumber('item');

Route::delete('cart/items', [CartController::class, 'clear']);

//leitura de catalogo
Route::apiResource('products', ProductController::class)
    ->only(['index', 'show'])
    ->whereNumber('product');

Route::apiResource('categories', CategorieController::class)
    ->only(['index', 'show'])
    ->whereNumber('category');


// =rotas protegidas=

Route::middleware('auth:sanctum')->group(function () {

    //perfil e sessão
    Route::get('me', [AuthController::class, 'me']);

    Route::put('me', [AuthController::class, 'updateProfile']);

    Route::put('me/password', [AuthController::class, 'changePassword']);

    Route::post('logout', [AuthController::class, 'logout']);
    
    //encomendas e pedidos
    Route::apiResource('orders', OrderController::class)
        ->only(['index', 'show'])
        ->whereNumber('order');

    Route::post('orders/checkout', [OrderController::class, 'checkout']);

    Route::post('orders/{order}/simulate-payment', [OrderController::class, 'simulatePayment'])
        ->whereNumber('order');

    Route::apiResource('order_items', Order_itemController::class)
        ->only(['index', 'show'])
        ->whereNumber('order_item');

        //endereços
    Route::apiResource('addresses', AddressController::class)
        ->only(['index', 'show', 'store', 'update', 'destroy'])
        ->whereNumber('address');

 // =protegido pelas role admin=
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class)
            ->except(['store'])
            ->whereNumber('user');

        Route::apiResource('products', ProductController::class)
            ->only(['store', 'update', 'destroy'])
            ->whereNumber('product');

        Route::apiResource('orders', OrderController::class)
            ->only(['store', 'destroy'])
            ->whereNumber('order');

        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])
            ->whereNumber('order');

        Route::apiResource('order_items', Order_itemController::class)
            ->only(['store', 'update', 'destroy'])
            ->whereNumber('order_item');

        Route::apiResource('categories', CategorieController::class)
            ->only(['store', 'update', 'destroy'])
            ->whereNumber('category');
    });
});
