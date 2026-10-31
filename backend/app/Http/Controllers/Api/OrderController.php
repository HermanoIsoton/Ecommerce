<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrdersRequest;
use App\Http\Requests\UpdateOrdersRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    public function index()
    {
        $orders = $this->orderService->listAll();

        return OrderResource::collection($orders);
    }

    public function store(StoreOrdersRequest $request)
    {
        $order = $this->orderService->create($request->validated());

        return new OrderResource($order);
    }

    public function show(string $id)
    {
        $order = $this->orderService->findById($id);

        return new OrderResource($order);
    }

    // soamente admin ->
    public function update(UpdateOrdersRequest $request, string $id)
    {
        $order = $this->orderService->update($id, $request->validated());

        return new OrderResource($order);
    }

    public function destroy(string $id)
    {
        $this->orderService->delete($id);

        return response()->noContent();
    }
}
