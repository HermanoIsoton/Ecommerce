<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Order_itemService;

class Order_itemController extends Controller
{
    public function __construct(
        private Order_itemService $order_itemService
    ) {}
   
    public function index()
    {
        $order_items = $this->order_itemService->listAll();
        return response()->json($order_items);
    }

   
    public function store(Request $request)
    {
        $order_item = $this->order_itemService->create($request->all());
        return response()->json($order_item, 201);
    }

    
    public function show(string $id)
    {
         $order_item = $this->order_itemService->findById($id);

        return response()->json($order_item);
    }

    // soamente admin ->
    public function update(Request $request, string $id)
    {
          $order_item = $this->order_itemService->update($id, $request->all());

        return response()->json($order_item);
    }


    public function destroy(string $id)
    {
        $this->order_itemService->delete($id);

        return response()->noContent();
    }
}
