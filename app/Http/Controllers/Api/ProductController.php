<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProductService;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}
   
    public function index()
    {
        $products = $this->productService->listAll();
        return response()->json($products);
    }

   // soamente admin ->
    public function store(Request $request)
    {
        $product = $this->productService->create($request->all());
        return response()->json($product, 201);
    }

    
    public function show(string $id)
    {
         $product = $this->productService->findById($id);

        return response()->json($product);
    }

   
    public function update(Request $request, string $id)
    {
          $product = $this->productService->update($id, $request->all());

        return response()->json($product);
    }


    public function destroy(string $id)
    {
        $this->productService->delete($id);

        return response()->noContent();
    }
}
