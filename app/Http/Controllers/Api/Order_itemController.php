<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderItemsRequest;
use App\Http\Requests\UpdateOrderItemsRequest;
use App\Http\Resources\Order_itemResource;
use App\Services\Order_itemService;

class Order_itemController extends Controller
{
    public function __construct(
        private Order_itemService $order_itemService
    ) {}

    public function index()
    {
        $order_items = $this->order_itemService->listAll();

        return  Order_itemResource::collection($order_items);
    }

    public function store(StoreOrderItemsRequest $request)
    {
        $order_item = $this->order_itemService->create($request->validated());

        return new Order_itemResource($order_item);
    }

    public function show(string $id)
    {
        $order_item = $this->order_itemService->findById($id);

        return new Order_itemResource($order_item);
    }

    // soamente admin ->
    public function update(UpdateOrderItemsRequest $request, string $id)
    {
        $order_item = $this->order_itemService->update($id, $request->validated());

        return new Order_itemResource($order_item);
    }

    public function destroy(string $id)
    {
        $this->order_itemService->delete($id);

        return response()->noContent();
    }
}
