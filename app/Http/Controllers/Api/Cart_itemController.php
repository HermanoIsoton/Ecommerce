<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Cart_itemService;

class Cart_itemController extends Controller
{
    public function __construct(
        private Cart_itemService $cart_itemService
    ) {}
   
    public function index()
    {
        $cart_items = $this->cart_itemService->listAll();
        return response()->json($cart_items);
    }

   
    public function store(Request $request)
    {
        $cart_item = $this->cart_itemService->create($request->all());
        return response()->json($cart_item, 201);
    }

    
    public function show(string $id)
    {
         $cart_item = $this->cart_itemService->findById($id);

        return response()->json($cart_item);
    }

   
    public function update(Request $request, string $id)
    {
          $cart_item = $this->cart_itemService->update($id, $request->all());

        return response()->json($cart_item);
    }


    public function destroy(string $id)
    {
        $this->cart_itemService->delete($id);

        return response()->noContent();
    }
}
