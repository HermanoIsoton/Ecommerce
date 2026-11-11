<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCartItemsRequest;
use App\Http\Requests\UpdateCartItemsRequest;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartService $cartService) {}

    public function createSession()
    {
        $session = $this->cartService->createGuestCart();

        return response()
            ->json([
                'data' => (new CartResource($session['cart']))->resolve(request()),
                'token' => $session['token'],
            ], 201)
            ->header('Cache-Control', 'no-store');
    }

    public function show(Request $request)
    {
        return response()->json([
            'data' => (new CartResource($this->cartService->getCart(
                $request->user(),
                $request->header('X-Cart-Token'),
            )))->resolve($request),
        ])->header('Cache-Control', 'no-store');
    }

    public function addItem(StoreCartItemsRequest $request)
    {
        $validated = $request->validated();
        $cart = $this->cartService->addItem(
            $request->user(),
            $request->header('X-Cart-Token'),
            (int) $validated['product_id'],
            (int) ($validated['quantity'] ?? 1),
        );

        return response()->json(['data' => (new CartResource($cart))->resolve($request)])->header('Cache-Control', 'no-store');
    }

    public function updateItem(UpdateCartItemsRequest $request, int $item)
    {
        $validated = $request->validated();
        $cart = $this->cartService->updateItem(
            $request->user(),
            $request->header('X-Cart-Token'),
            $item,
            (int) $validated['quantity'],
        );

        return response()->json(['data' => (new CartResource($cart))->resolve($request)])->header('Cache-Control', 'no-store');
    }

    public function removeItem(Request $request, int $item)
    {
        $cart = $this->cartService->removeItem(
            $request->user(),
            $request->header('X-Cart-Token'),
            $item,
        );

        return response()->json(['data' => (new CartResource($cart))->resolve($request)])->header('Cache-Control', 'no-store');
    }

    public function clear(Request $request)
    {
        $cart = $this->cartService->clear(
            $request->user(),
            $request->header('X-Cart-Token'),
        );

        return response()->json(['data' => (new CartResource($cart))->resolve($request)])->header('Cache-Control', 'no-store');
    }
}
