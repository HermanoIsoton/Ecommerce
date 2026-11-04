<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCartsRequest;
use App\Http\Requests\UpdateCartsRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function index(Request $request)
    {
        $carts = $request->user()->role === 'admin'
            ? $this->cartService->listAll()
            : Cart::query()->where('user_id', $request->user()->id)->get();

        return CartResource::collection($carts);
    }

    public function store(StoreCartsRequest $request)
    {
        $cart = $this->cartService->create($request->validated());

        return new CartResource($cart);
    }

    public function show(Request $request, string $id)
    {
        $cart = $request->user()->role === 'admin'
            ? $this->cartService->findById($id)
            : Cart::query()
                ->where('user_id', $request->user()->id)
                ->findOrFail($id);

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
