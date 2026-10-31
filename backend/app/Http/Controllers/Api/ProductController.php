<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductsRequest;
use App\Http\Requests\UpdateProductsRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    public function index()
    {
        $products = $this->productService->listAll();

        return ProductResource::collection($products);
    }

    // soamente admin ->
    public function store(StoreProductsRequest $request)
    {
        $product = $this->productService->create($request->validated());

        return new ProductResource($product);
    }

    public function show(string $id)
    {
        $product = $this->productService->findById($id);

        return new ProductResource($product);
    }

    public function update(UpdateProductsRequest $request, string $id)
    {
        $product = $this->productService->update($id, $request->validated());

        return new ProductResource($product);
    }

    public function destroy(string $id)
    {
        $this->productService->delete($id);

        return response()->noContent();
    }
}
