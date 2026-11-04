<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrdersRequest;
use App\Http\Requests\UpdateOrdersRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    public function index(Request $request)
    {
        $orders = $request->user()->role === 'admin'
            ? $this->orderService->listAll()
            : Order::query()->where('user_id', $request->user()->id)->get();

        return OrderResource::collection($orders);
    }

    public function store(StoreOrdersRequest $request)
    {
        $order = $this->orderService->create($request->validated());

        return new OrderResource($order);
    }

    public function show(Request $request, string $id)
    {
        $order = $request->user()->role === 'admin'
            ? $this->orderService->findById($id)
            : Order::query()
                ->where('user_id', $request->user()->id)
                ->findOrFail($id);

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
