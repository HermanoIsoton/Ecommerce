<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCartsRequest;
use App\Http\Requests\UpdateCartsRequest;
use App\Http\Resources\CartResource;
use App\Services\CartService;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function index()
    {
        $carts = $this->cartService->listAll();

        return CartResource::collection($carts);
    }

    public function store(StoreCartsRequest $request)
    {
        $cart = $this->cartService->create($request->validated());

        return new CartResource($cart);
    }

    public function show(string $id)
    {
        $cart = $this->cartService->findById($id);

        return new CartResource($cart);
    }

    public function update(UpdateCartsRequest $request, string $id)
    {
        $cart = $this->cartService->update($id, $request->validated());

        return new CartResource($cart);
    }

    public function destroy(string $id)
    {
        $this->cartService->delete($id);

        return response()->noContent();
    }
}
