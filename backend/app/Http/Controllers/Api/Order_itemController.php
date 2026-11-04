<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderItemsRequest;
use App\Http\Requests\UpdateOrderItemsRequest;
use App\Http\Resources\Order_itemResource;
use App\Models\Order;
use App\Models\Order_item;
use App\Services\Order_itemService;
use Illuminate\Http\Request;

class Order_itemController extends Controller
{
    public function __construct(
        private Order_itemService $order_itemService
    ) {}

    public function index(Request $request)
    {
        $order_items = $request->user()->role === 'admin'
            ? $this->order_itemService->listAll()
            : Order_item::query()
                ->whereIn('order_id', Order::query()
                    ->where('user_id', $request->user()->id)
                    ->select('id'))
                ->get();

        return Order_itemResource::collection($order_items);
    }

    public function store(StoreOrderItemsRequest $request)
    {
        $order_item = $this->order_itemService->create($request->validated());

        return new Order_itemResource($order_item);
    }

    public function show(Request $request, string $id)
    {
        $order_item = $request->user()->role === 'admin'
            ? $this->order_itemService->findById($id)
            : Order_item::query()
                ->whereIn('order_id', Order::query()
                    ->where('user_id', $request->user()->id)
                    ->select('id'))
                ->findOrFail($id);

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
