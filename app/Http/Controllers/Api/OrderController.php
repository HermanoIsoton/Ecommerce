<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}
   
    public function index()
    {
        $orders = $this->orderService->listAll();
        return response()->json($orders);
    }

   
    public function store(Request $request)
    {
        $order = $this->orderService->create($request->all());
        return response()->json($order, 201);
    }

    
    public function show(string $id)
    {
         $order = $this->orderService->findById($id);

        return response()->json($order);
    }

    // soamente admin ->
    public function update(Request $request, string $id)
    {
          $order = $this->orderService->update($id, $request->all());

        return response()->json($order);
    }


    public function destroy(string $id)
    {
        $this->orderService->delete($id);

        return response()->noContent();
    }
}
