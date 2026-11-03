<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoriesRequest;
use App\Http\Requests\UpdateCategoriesRequest;
use App\Http\Resources\CategorieResource;
use App\Services\CategorieService;

class CategorieController extends Controller
{
    public function __construct(
        private CategorieService $categorieService
    ) {}

    public function index()
    {
        $categories = $this->categorieService->listAll();

        return CategorieResource::collection($categories);
    }

    public function store(StoreCategoriesRequest $request)
    {

        $categorie = $this->categorieService->create($request->validated());

        return new CategorieResource($categorie);
    }

    public function show(string $id)
    {
        $categorie = $this->categorieService->findById($id);

        return new CategorieResource($categorie);
    }

    public function update(UpdateCategoriesRequest $request, string $id)
    {
        $categorie = $this->categorieService->update($id, $request->validated());

        return new CategorieResource($categorie);
    }

    public function destroy(string $id)
    {
        $this->categorieService->delete($id);

        return response()->noContent();
    }
}
