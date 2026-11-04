<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCartItemsRequest;
use App\Http\Requests\UpdateCartItemsRequest;
use App\Http\Resources\Cart_itemResource;
use App\Models\Cart;
use App\Models\Cart_item;
use App\Services\Cart_itemService;
use Illuminate\Http\Request;

class Cart_itemController extends Controller
{
    public function __construct(
        private Cart_itemService $cart_itemService
    ) {}

    public function index(Request $request)
    {
        $cart_items = $request->user()->role === 'admin'
            ? $this->cart_itemService->listAll()
            : Cart_item::query()
                ->whereIn('cart_id', Cart::query()
                    ->where('user_id', $request->user()->id)
                    ->select('id'))
                ->get();

        return Cart_itemResource::collection($cart_items);
    }

    public function store(StoreCartItemsRequest $request)
    {
        $cart_item = $this->cart_itemService->create($request->validated());

        return new Cart_itemResource($cart_item);
    }

    public function show(Request $request, string $id)
    {
        $cart_item = $request->user()->role === 'admin'
            ? $this->cart_itemService->findById($id)
            : Cart_item::query()
                ->whereIn('cart_id', Cart::query()
                    ->where('user_id', $request->user()->id)
                    ->select('id'))
                ->findOrFail($id);

        return new Cart_itemResource($cart_item);
    }

    public function update(UpdateCartItemsRequest $request, string $id)
    {
        $cart_item = $this->cart_itemService->update($id, $request->validated());

        return new Cart_itemResource($cart_item);
    }

    public function destroy(string $id)
    {
        $this->cart_itemService->delete($id);

        return response()->noContent();
    }
}
