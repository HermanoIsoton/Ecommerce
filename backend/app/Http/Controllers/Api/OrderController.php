<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutOrderRequest;
use App\Http\Requests\StoreOrdersRequest;
use App\Http\Requests\UpdateOrdersRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\CheckoutService;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private CheckoutService $checkoutService,
    ) {}

    public function index(Request $request)
    {
        $this->checkoutService->expirePendingOrders();
        $orders = $request->user()->role === 'admin'
            ? Order::query()->with('orderItems')->latest()->get()
            : Order::query()->with('orderItems')->where('user_id', $request->user()->id)->latest()->get();

        return OrderResource::collection($orders);
    }

    public function checkout(CheckoutOrderRequest $request)
    {
        $order = $this->checkoutService->createOrder(
            $request->user(),
            (int) $request->validated('address_id'),
        );

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(201)
            ->header('Cache-Control', 'no-store');
    }

    public function simulatePayment(Request $request, int $order)
    {
        [$updatedOrder, $expired] = $this->checkoutService->simulatePayment(
            $request->user(),
            $order,
        );

        if ($expired) {
            return response()->json([
                'message' => 'O prazo de pagamento expirou. O pedido foi cancelado e o estoque foi liberado.',
                'data' => (new OrderResource($updatedOrder))->resolve($request),
            ], 409)->header('Cache-Control', 'no-store');
        }

        return (new OrderResource($updatedOrder))
            ->response()
            ->header('Cache-Control', 'no-store');
    }

    public function store(StoreOrdersRequest $request)
    {
        $order = $this->orderService->create($request->validated());

        return new OrderResource($order);
    }

    public function show(Request $request, string $id)
    {
        $order = $request->user()->role === 'admin'
            ? Order::query()->with('orderItems')->findOrFail($id)
            : Order::query()
                ->where('user_id', $request->user()->id)
                ->findOrFail($id);
        $order = $this->checkoutService->expireIfDue($order);

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
