<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CartService;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {}
   
    public function index()
    {
        $carts = $this->cartService->listAll();
        return response()->json($carts);
    }

   
    public function store(Request $request)
    {
        $cart = $this->cartService->create($request->all());
        return response()->json($cart, 201);
    }

    
    public function show(string $id)
    {
         $cart = $this->cartService->findById($id);

        return response()->json($cart);
    }

   
    public function update(Request $request, string $id)
    {
          $cart = $this->cartService->update($id, $request->all());

        return response()->json($cart);
    }


    public function destroy(string $id)
    {
        $this->cartService->delete($id);

        return response()->noContent();
    }
}
