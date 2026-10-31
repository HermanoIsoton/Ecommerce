<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCartItemsRequest;
use App\Http\Requests\UpdateCartItemsRequest;
use App\Http\Resources\Cart_itemResource;
use App\Services\Cart_itemService;

class Cart_itemController extends Controller
{
    public function __construct(
        private Cart_itemService $cart_itemService
    ) {}

    public function index()
    {
        $cart_items = $this->cart_itemService->listAll();

        return Cart_itemResource::collection($cart_items);
    }

    public function store(StoreCartItemsRequest $request)
    {
        $cart_item = $this->cart_itemService->create($request->validated());

        return new Cart_itemResource($cart_item);
    }

    public function show(string $id)
    {
        $cart_item = $this->cart_itemService->findById($id);

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
